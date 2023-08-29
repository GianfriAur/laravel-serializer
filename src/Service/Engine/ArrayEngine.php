<?php /** @noinspection PhpClassCanBeReadonlyInspection */

namespace Gianfriaur\Serializer\Service\Engine;

use Countable;
use Gianfriaur\Serializer\Exception\MissingGetTypeStrategyException;
use Gianfriaur\Serializer\Exception\MissingMetadataParameterException;
use Gianfriaur\Serializer\Exception\ObjectMissingMethodException;
use Gianfriaur\Serializer\Exception\ObjectMissingPropertyException;
use Gianfriaur\Serializer\Exception\RecursiveSerializationException;
use Gianfriaur\Serializer\Exception\SerializationIsNotAllowedForTypeException;
use Gianfriaur\Serializer\Exception\SerializationMissingGetStrategyException;
use Gianfriaur\Serializer\Exception\SerializePrimitiveException;
use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use stdClass;

class ArrayEngine implements EngineInterface
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    public function __construct(
        private readonly Application         $app,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function getEngineName(): string
    {
        return 'array';
    }

    /**
     * @param mixed $object
     * @param $metadata
     * @return \Closure
     * @throws MissingGetTypeStrategyException
     */
    private function getObjectProperty(mixed $object, $metadata): \Closure
    {
        $metadata['type'] = $metadata['type'] ?? 'unknown';


        return match ($metadata['type']) {
            'direct' => function () use ($object, $metadata) {
                if (!isset($metadata['property'])) {
                    throw new MissingMetadataParameterException('property');
                }

                /* if ($object instanceof Model) {
                    if ($object->getAttribute($metadata['property'])) {

                    }
                } */

                if (property_exists($object, $metadata['property'])) {
                    return $object->{$metadata['property']};
                }
                if (method_exists($object, 'getAttribute')) {
                    return $object->getAttribute($metadata['property']);
                }
                throw new ObjectMissingPropertyException($object, $metadata['property']);
            },
            'function' => function () use ($object, $metadata) {
                if (!isset($metadata['name'])) {
                    throw new MissingMetadataParameterException('name');
                }
                if (!isset($metadata['args'])) {
                    throw new MissingMetadataParameterException('args');
                }

                if (
                    !method_exists($object, $metadata['name'])
                    && !( // property is callable
                        property_exists($object, $metadata['name'])
                        && is_callable($object->{$metadata['name']})
                    )
                ) {
                    throw new ObjectMissingMethodException($object, $metadata['name']);
                }
                if ($object instanceof stdClass) {
                    return ($object->{$metadata['name']})(...$metadata['args']);
                } else {
                    return $object->{$metadata['name']}(...$metadata['args']);
                }
            },
            default => throw new MissingGetTypeStrategyException($metadata['type'])
        };
    }

    private function clearArray($array):?array{

        // if is array of arrays
        if (array_reduce(array_map(fn($a)=>is_array($a), $array), fn($a, $b)=>$a && $b, true)){
            foreach ($array as $key => $value) {
                $array[$key] = $this->clearArray($value);
            }
        }

        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function serializeWithMetadata(mixed $object, array $serialization_metadata, ?array $serializationStack = [], array $options = []):?array
    {

        $serialized = [];
        foreach ($serialization_metadata['properties'] as $name => $description) {

            // override name if it has the override
            if (isset($description['name'])) {
                $name = $description['name'];
            }

            // check if exist an extraction strategy
            if (!isset($description['get'])) {
                throw new SerializationMissingGetStrategyException($name);
            }

            // get value
            $value = $this->getObjectProperty($object, $description['get'])();

            // set default value
            if (!$value && isset($description['default'])) {
                $value = $description['default'];
            }

            $is_not_allowed = in_array(
                gettype($value)
                , ['resource', 'resource (closed)', 'unknown type']
            );

            if ($is_not_allowed) {
                throw new SerializationIsNotAllowedForTypeException($name, gettype($value));
            }

            // manage Array type
            $is_array = gettype($value) === 'array';

            if ($is_array) {
                $new_value = [];

                foreach ($value as $key => $value_element) {
                    $is_primitive = in_array(
                        gettype($value)
                        , ['boolean', 'integer', 'double', 'string', 'NULL']
                    );
                    if ($is_primitive) {
                        // if primitive assign
                        $new_value[$key] = $value_element;
                    } else {
                        // if not primitive navigate in to object
                        $nested_groups = $description['groups'] ?? $serialization_metadata['groups'];
                        $nested_metadata_service = $description['metadata_service'] ?? $serialization_metadata['metadata_service'];

                        $new_value[$key] = $this->serializer->serialize($value, $nested_groups, $this->getEngineName(), $nested_metadata_service, $serializationStack);
                    }
                }
                //override value
                $value = $new_value;

            } else {
                // manage not array
                $is_primitive = in_array(
                    gettype($value)
                    , ['boolean', 'integer', 'double', 'string', 'NULL']
                );

                // if not primitive navigate in to object
                if (!$is_primitive) {
                    $nested_groups = $description['groups'] ?? $serialization_metadata['groups'];
                    $nested_metadata_service = $description['metadata_service'] ?? $serialization_metadata['metadata_service'];

                    $value = $this->serializer->serialize($value, $nested_groups, $this->getEngineName(), $nested_metadata_service, $serializationStack);
                }
            }
            $serialized[$name] = $value;
        }

        return $serialized;
    }

    private function serializeElement(mixed $object, array $groups, ?string $metadataProviderClass,  ?array $serializationStack = [], array $options = []){

        $serialization_metadata = $this->serializer->getObjectSerializationMetadata($object, $groups, $metadataProviderClass);

        if (!$serialization_metadata) {
            return $this->getEmptySerialization();
        }

        if (in_array($object,$serializationStack)){
            if ($options['prevent_recursive_serialization'] === true) {
                return null;
            }else{
                throw new RecursiveSerializationException();
            }
        }else{
            $serializationStack[] = $object;
        }

        $serialized = $this->serializeWithMetadata($object, $serialization_metadata,$serializationStack,$options);

        if ($serialized == $this->getEmptySerialization() && $options['serialize_empty_as_null'] === true) {
            return null;
        }

        return $serialized;
    }

    /**
     * @param mixed $object
     * @param $serialization_metadata
     * @return array
     * @throws MissingGetTypeStrategyException
     */
    public function serializeObject(mixed $object,array $group, ?string $metadataProviderClass ,?array $serializationStack = [], array $options = []): ?array
    {

        if ($object === null && $options['serialize_null_as_null'] === true) {
            return null;
        }

        $is_primitive = in_array(
            gettype($object)
            , ['boolean', 'integer', 'double', 'string']
        );

        if ($is_primitive) {
            if ($options['serialize_primitive'] === true) {
                return [$object];
            } else {
                throw new SerializePrimitiveException();
            }

        }

        if (is_array($object) || $object instanceof Countable) {
            $serialized = (new Collection($object))->map(
                fn($object) => $this->serializeElement($object, $group,$metadataProviderClass, $serializationStack,$options)
            )->toArray();
        } else {
            $serialized = $this->serializeElement($object, $group,$metadataProviderClass ,$serializationStack,$options);
        }

        if ($serialized == $this->getEmptySerialization() && $options['serialize_empty_as_null'] === true) {
            return null;
        }

        if ($options['serialize_null'] === false) {
            $serialized =  $this->clearArray($serialized);
        }

        return $serialized;
    }


    public function getEmptySerialization(): array
    {
        return [];
    }
}
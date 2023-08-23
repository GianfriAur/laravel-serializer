<?php /** @noinspection PhpClassCanBeReadonlyInspection */

namespace Gianfriaur\Serializer\Service\Engine;

use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;
use Illuminate\Foundation\Application;

class ArrayEngine implements EngineInterface
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    public function __construct(
        private readonly Application $app,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function getEngineName(): string
    {
        return 'array';
    }

    private function getObjectProperty(mixed $object,$metadata):mixed{
        $metadata['type'] = $metadata['type'] ?? 'unknown';
        return match ($metadata['type']){
            'direct' => function() use($object,$metadata){
                if(!isset($metadata['property'])){
                    // TODO: throw missing property configuration metadata exception
                }
                if(!property_exists($object,$metadata['property'])){
                    // TODO: throw object haven't property  exception
                }
                return $object->{$metadata['property']};
            },
            'function'=> function() use($object,$metadata){
                if(!isset($metadata['name'])){
                    // TODO: throw missing name configuration metadata exception
                }
                if(!isset($metadata['args'])){
                    // TODO: throw missing args configuration metadata exception
                }
                if(!method_exists($object,$metadata['name'])){
                    // TODO: throw object haven't function named name exception
                }
                return $object->{$metadata['name']}(...$metadata['args']);
            },
            default => throw new \Exception('') // TODO: throw get strategy missing type exception
        };
    }

    /**
     * @throws \Exception
     */
    public function serializeObject(mixed $object, $serialization_metadata): mixed
    {
        $serialized = [];
        foreach ($serialization_metadata['properties'] as $name => $description){

            // override name if it has the override
            if (isset($description['name'])){
                $name = $description['name'];
            }

            // check if exist an extraction strategy
            if (!isset($description['get'])){
                // TODO: throw missing get strategy exception
            }

            // get value
            $value = $this->getObjectProperty($object,$description['get']);

            // set default value
            if (!$value && isset($description['default'])){
                $value = $description['default'];
            }

            $is_not_allowed = in_array(
                gettype($value)
                ,['resource','resource (closed)', 'unknown type']
            );

            if($is_not_allowed){
                // TODO: throw serialization on TYPE is not allowed exception
            }

            // manage Array type
            $is_array = gettype($value);

            if ($is_array){
                $new_value=[];

                foreach ($value as $key =>$value_element){
                    $is_primitive = in_array(
                        gettype($value)
                        ,['boolean','integer', 'double','string', 'NULL']
                    );
                    if ($is_primitive){
                        // if primitive assign
                        $new_value[$key]=$value_element;
                    }else{
                        // if not primitive navigate in to object
                        $nested_groups = $description['groups'] ?? $serialization_metadata['groups'];
                        $nested_metadata_service = $description['metadata_service'] ?? $serialization_metadata['metadata_service'];

                        $new_value[$key] = $this->serializer->serialize($value,$nested_groups, $this->getEngineName(),$nested_metadata_service);
                    }
                }
                //override value
                $value = $new_value;

            }else{
                // manage not array
                $is_primitive = in_array(
                    gettype($value)
                    ,['boolean','integer', 'double','string', 'NULL']
                );

                // if not primitive navigate in to object
                if (!$is_primitive){
                    $nested_groups = $description['groups'] ?? $serialization_metadata['groups'];
                    $nested_metadata_service = $description['metadata_service'] ?? $serialization_metadata['metadata_service'];

                    $value = $this->serializer->serialize($value,$nested_groups, $this->getEngineName(),$nested_metadata_service);

                }
            }

            $serialized[$name] = $value;
        }

        return $serialized;
    }


    public function getEmptySerialization(): mixed
    {
        return [];
    }
}
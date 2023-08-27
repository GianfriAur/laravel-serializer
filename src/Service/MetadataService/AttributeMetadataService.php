<?php

namespace Gianfriaur\Serializer\Service\MetadataService;

use Gianfriaur\Serializer\Attribute\AbstractSerializeAttribute;
use Gianfriaur\Serializer\Attribute\GetParameter;
use Gianfriaur\Serializer\Attribute\Group;
use Gianfriaur\Serializer\Attribute\Groups;
use Gianfriaur\Serializer\Attribute\MetadataProvider;
use Gianfriaur\Serializer\Attribute\Name;
use Gianfriaur\Serializer\Attribute\SetParameter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

use ReflectionClass;

class AttributeMetadataService extends DefaultMetadataService
{

    public function __construct(private readonly array $options)
    {
        parent::__construct();
    }

    private function getOptions()
    {
        return [
            ...[
                'cache_store' => 'serializations',
                'has_cache' => false
            ],
            ...$this->options
        ];
    }


    private function getKey(string $object, array $groups)
    {
        return md5($object . '-' . join('-', $groups));
    }

    private function classHasSerializationAttributes(string $className): bool
    {
        $reflection = new ReflectionClass($className);
        foreach ($reflection->getAttributes() as $attribute) {
            if ($instance = $attribute->newInstance()) {
                if ($instance instanceof AbstractSerializeAttribute) {
                    return true;
                }
            }
        }
        return false;
    }


    /** @noinspection PhpUnused */
    public function manageGroupAttribute(Group $instance): array
    {
        $metadata = [$instance->name => ['' => []]];
        foreach ($instance->parameters as $key => $parameter) {

            if (is_numeric($key)) {
                if (!is_string($parameter)) {
                    // throw exception Group parameters is malformed
                }
                $metadata[$instance->name]['properties'][$parameter] = [
                    'get' => ['type' => 'direct', 'property' => $parameter],
                    'set' => ['type' => 'direct', 'property' => $parameter],
                    'name' => $parameter
                ];
            }

            if (is_string($key) && is_string($parameter)) {
                $metadata[$instance->name]['properties'][$key] = [
                    'get' => ['type' => 'direct', 'property' => $key],
                    'set' => ['type' => 'direct', 'property' => $key],
                    'name' => $parameter
                ];
            }

            if (is_string($key) && $parameter instanceof AbstractSerializeAttribute) {

                if ($parameter instanceof Group) {
                    // throw exception Group can't have Group in parameters
                }

                if ($parameter instanceof GetParameter) {
                    $metadata[$instance->name]['properties'][$key] = [
                        'get' => ['type' => 'function', 'name' => $parameter->method_name, 'args' => $parameter->args ?? []],
                        'name' => $key
                    ];
                }
                if ($parameter instanceof SetParameter) {
                    $metadata[$instance->name]['properties'][$key] = [
                        'set' => ['type' => 'function', 'name' => $parameter->method_name, 'args' => $parameter->args ?? []],
                        'name' => $key
                    ];
                }
                if ($parameter instanceof Groups) {
                    $metadata[$instance->name]['properties'][$key] = [
                        'groups' => $parameter->groups
                    ];
                }
                if ($parameter instanceof MetadataProvider) {
                    $metadata[$instance->name]['properties'][$key] = [
                        'metadata_service' => $parameter->metadataProviderClass
                    ];
                }
                if ($parameter instanceof Name) {
                    $metadata[$instance->name]['properties'][$key] = [
                        'metadata_service' => $parameter->name
                    ];
                }
            }
            if (is_string($key)) {

                foreach ($parameter as $parameter_element) {
                    if ($parameter_element instanceof Group) {
                        // throw exception Group can't have Group in parameters
                    }

                    if ($parameter_element instanceof GetParameter) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'get' => ['type' => 'function', 'name' => $parameter_element->method_name, 'args' => $parameter_element->args ?? []],
                            'name' => $key
                        ];
                    }
                    if ($parameter_element instanceof SetParameter) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'set' => ['type' => 'function', 'name' => $parameter_element->method_name, 'args' => $parameter_element->args ?? []],
                            'name' => $key
                        ];
                    }
                    if ($parameter_element instanceof Groups) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'groups' => $parameter_element->groups
                        ];
                    }
                    if ($parameter_element instanceof MetadataProvider) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'metadata_service' => $parameter_element->metadataProviderClass
                        ];
                    }
                    if ($parameter_element instanceof Name) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'metadata_service' => $parameter_element->name
                        ];
                    }

                    if (is_string($parameter_element)) {
                        $metadata[$instance->name]['properties'][$key] = [
                            'get' => ['type' => 'direct', 'property' => $key],
                            'set' => ['type' => 'direct', 'property' => $key],
                            'name' => $parameter_element
                        ];
                    }
                }
            }
        }

        return $metadata;

    }

    private function getClassFullMetadataFromClassName(string $className): ?array
    {
        $metadata = [];

        $reflection = new ReflectionClass($className);
        foreach ($reflection->getAttributes() as $attribute) {
            if ($instance = $attribute->newInstance()) {
                if ($instance instanceof AbstractSerializeAttribute) {

                    $instance->validate();
                    $metadata = $this->metadataMergeRecursive($metadata, $instance->injectMetadata());

                  /*  $attribute_name = last(explode('\\', $instance::class));

                    $mix_in_metadata = null;

                    if (method_exists($this, "manage{$attribute_name}Attribute")) {
                        $mix_in_metadata = $this->{"manage{$attribute_name}Attribute"}($instance);
                    }


                    if ($mix_in_metadata !== null) {
                        $metadata = $this->metadataMergeRecursive($metadata, $mix_in_metadata);
                    }
*/
                }
            }
        }
        return $metadata === [] ? null : $metadata;
    }

    public function prepareClassSerializationAttributes(string $className): void
    {
        if ($this->getOptions()['has_cache']) {
            $is_volatile_memory = (app()->hasDebugModeEnabled() || !App::isProduction()) === true;
            $allClassMetadata = $is_volatile_memory
                ? Cache::store($this->getOptions()['cache_store'])->remember($className, 1, fn() => $this->getClassFullMetadataFromClassName($className))
                : Cache::store($this->getOptions()['cache_store'])->rememberForever($className, fn() => $this->getClassFullMetadataFromClassName($className));
        } else {
            $allClassMetadata = $this->getClassFullMetadataFromClassName($className);
        }
        $this->data[$className] = $allClassMetadata;
    }

    public function hasSerializationMetadata(?string $object, array $groups): bool
    {

        if ($object === null) return false;

        if (!isset($this->data[$object])) {
            if ($this->classHasSerializationAttributes($object)) {
                $this->prepareClassSerializationAttributes($object);
            }
        }

        foreach ($this->data[$object] as $group_name => $group_metadata) {
            if (in_array($group_name, $groups)) {
                return true;
            }
        }

        return false;
    }

    public function hasSerializationMetadata_bis(mixed $object, array $groups): bool
    {


        $key = md5(gettype($object) . '-' . join('-', $groups));


    }

    public function getSerializationMetadata(mixed $object, array $groups): mixed
    {
        if (!isset($this->data[$object])) {
            if ($this->classHasSerializationAttributes($object)) {
                $this->prepareClassSerializationAttributes($object);
            }
        }

        return parent::getSerializationMetadata($object, $groups);
    }
}
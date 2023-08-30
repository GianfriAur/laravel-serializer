<?php

namespace Gianfriaur\Serializer\Service\MetadataService;

use Gianfriaur\Serializer\Attribute\AbstractSerializeAttribute;
use Gianfriaur\Serializer\Attribute\Get;
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

    private function getClassFullMetadataFromClassName(string $className): ?array
    {
        $metadata = [];

        $reflection = new ReflectionClass($className);
        foreach ($reflection->getAttributes() as $attribute) {
            if ($instance = $attribute->newInstance()) {
                if ($instance instanceof AbstractSerializeAttribute) {

                    $instance->validate();
                    $metadata = $this->metadataMergeRecursive($metadata, $instance->injectMetadata());

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
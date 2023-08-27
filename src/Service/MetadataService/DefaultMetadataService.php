<?php

namespace Gianfriaur\Serializer\Service\MetadataService;

class DefaultMetadataService implements MetadataServiceInterface
{

    protected array $data = [];


    public function __construct()
    {

    }

    private function createObjectMetadataIfNotExist($objectClass): void
    {
        if (!array_key_exists($objectClass,$this->data)) $this->data[$objectClass] = [];
    }
    private function createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName,): void
    {
        if (!array_key_exists($propertyName,$this->data[$objectClass][$group]['properties'])) $this->data[$objectClass][$group]['properties'][$propertyName] = [];
    }

    public function addGroup($objectClass, $name, array $properties): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->data[$objectClass][$name] = ['properties' => $properties];
    }

    public function addProperty($objectClass, $group, $propertyName, $propertyDescription): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->data[$objectClass][$group]['properties'][$propertyName] = $propertyDescription;
    }

    public function addPropertyGetter($objectClass, $group, $propertyName, $propertyGetterDescription): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName);
        $this->data[$objectClass][$group]['properties'][$propertyName]['get'] = $propertyGetterDescription;
    }

    public function addPropertyDirectGetter($objectClass, $group, $propertyName, $propertyOriginalName = null): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        if (!$propertyOriginalName) $propertyOriginalName = $propertyName;
        $this->addPropertyGetter($objectClass, $group, $propertyName,['type' => 'direct', 'property' => $propertyOriginalName]);
    }

    public function addPropertyFunctionGetter($objectClass, $group, $propertyName, $functionName, array $functionArgs = []): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->addPropertyGetter($objectClass, $group, $propertyName,['type' => 'function', 'name' => $functionName, 'args' => $functionArgs]);
    }

    public function addPropertySetter($objectClass, $group, $propertyName, $propertyGetterDescription): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName);
        $this->data[$objectClass][$group]['properties'][$propertyName]['set'] = $propertyGetterDescription;
    }

    public function addPropertyDirectSetter($objectClass, $group, $propertyName, $propertyOriginalName = null): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        if (!$propertyOriginalName) $propertyOriginalName = $propertyName;
        $this->addPropertySetter($objectClass, $group, $propertyName,['type' => 'direct', 'property' => $propertyOriginalName]);
    }

    public function addPropertyFunctionSetter($objectClass, $group, $propertyName, $functionName, array $functionArgs = []): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->addPropertySetter($objectClass, $group, $propertyName,['type' => 'function', 'name' => $functionName, 'args' => $functionArgs]);
    }

    public function addPropertySerializedName($objectClass, $group, $propertyName, $serializedName): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName);
        $this->data[$objectClass][$group]['properties'][$propertyName]['name'] = $serializedName;
    }

    public function addPropertyGroups($objectClass, $group, $propertyName, $propertyGroups): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName);
        $this->data[$objectClass][$group]['properties'][$propertyName]['groups'] = $propertyGroups;
    }

    public function addPropertyDefaultValue($objectClass, $group, $propertyName, mixed $defaultValue): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->data[$objectClass][$group]['properties'][$propertyName]['default'] = $defaultValue;
    }

    public function addPropertyMetadataServiceProvider($objectClass, $group, $propertyName, $metadataServiceClass): void
    {
        $this->createObjectMetadataIfNotExist($objectClass);
        $this->createObjectPropertyMetadataIfNotExist($objectClass, $group, $propertyName);
        $this->data[$objectClass][$group]['properties'][$propertyName]['metadata_service'] = $metadataServiceClass;
    }

    public function hasSerializationMetadata(?string $object, array $groups): bool
    {
        if ($object === null) return false;

        foreach ($this->data[$object] as $group_name => $group_metadata) {
            if (in_array($group_name, $groups)) {
                return true;
            }
        }
        return false;
    }

    protected function metadataMergeRecursive(...$arrays)
    {
        if (sizeof($arrays) < 2) {
            return;
        }
        $merged = array();
        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                return;
            }
            if (!$array)
                continue;
            foreach ($array as $key => $value)
                if (is_string($key))
                    if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                        $merged[$key] = $this->metadataMergeRecursive($merged[$key], $value);
                    else
                        $merged[$key] = $value;
                else
                    $merged[] = $value;
        }
        return (is_array($merged) && $merged===array_filter($merged, 'is_string')) ? array_unique($merged,SORT_REGULAR) : $merged;
    }


    public function getSerializationMetadata(mixed $object, array $groups): mixed
    {
        $groups_metadata = [];
        foreach ($this->data[$object] as $group_name => $group_metadata) {
            if (in_array($group_name, $groups)) {
                $groups_metadata[] = $group_metadata;
            }
        }
        if (sizeof($groups_metadata) ===0 ){
            return [
                'properties' =>[],
                'groups' => $groups,
                'metadata_service' => $this::class,
            ];
        }
        return [
            'properties' => (sizeof($groups_metadata)>1 ? ($this->metadataMergeRecursive(...$groups_metadata)):($groups_metadata[0]))['properties'],
            'groups' => $groups,
            'metadata_service' => $this::class,
        ];
    }
}
<?php

namespace Gianfriaur\Serializer\Service\MetadataService;

interface MetadataServiceInterface
{

    public function hasSerializationMetadata(?string $object, array $groups):bool;
    public function getSerializationMetadata(mixed $object, array $groups):mixed;
}
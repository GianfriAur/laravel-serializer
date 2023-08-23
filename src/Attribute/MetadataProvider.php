<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MetadataProvider
{
    public function __construct(
        public string $metadataProviderClass,
        public ?array $groups=null,
        public ?array $args=null,
    )
    {}
}
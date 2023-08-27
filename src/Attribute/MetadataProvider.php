<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MetadataProvider extends AbstractSerializeAttribute
{
    public function __construct(
        public string $metadataProviderClass,
        public ?array $ref_groups=null,
        public ?array $args=null,
    )
    {}
    function injectMetadata(): array
    {
        return [];
    }

    function validate()
    {
        $this->hasParametersOrThrowException([]);
        return true;
    }

}
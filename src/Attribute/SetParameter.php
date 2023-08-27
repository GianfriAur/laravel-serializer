<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SetParameter extends AbstractSerializeAttribute
{
    public function __construct(
        public string $method_name,
        public ?string $parameter_name=null,
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
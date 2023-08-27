<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;

#[\Attribute(\Attribute::TARGET_CLASS)]
class GetParameter extends AbstractSerializeAttribute
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

    /**
     * @throws MissingParameterException
     */
    function validate()
    {
        $this->hasParametersOrThrowException([]);
        return true;
    }
}
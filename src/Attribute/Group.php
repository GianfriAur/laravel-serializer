<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group extends AbstractSerializeAttribute
{
    public function __construct(
        public string $name,
        public array $parameters,
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
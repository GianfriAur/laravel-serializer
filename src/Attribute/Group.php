<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group
{
    public function __construct(
        public string $name,
        public array $parameters,
    )
    {}
}
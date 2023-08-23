<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Name
{
    public function __construct(
        public string $name,
        public ?string $parameter_name=null,
        public ?array $groups=null,
        public ?array $args=null,
    )
    {}
}
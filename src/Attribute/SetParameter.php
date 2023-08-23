<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SetParameter
{
    public function __construct(
        public string $method_name,
        public ?string $parameter_name=null,
        public ?array $groups=null,
        public ?array $args=null,
    )
    {}
}
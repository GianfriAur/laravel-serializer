<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class SetThrough extends AbstractSerializeAttribute
{
    public function __construct(
        public string  $method_name,
        public ?array  $args = null,
        public ?string $parameter_name = null,
        public ?array  $ref_groups = null,
        public string  $type = 'function'
    )
    {
        parent::__construct($parameter_name,$ref_groups);
    }

    function injectMetadata(): array
    {
        $metadata = [];

        foreach ($this->ref_groups ?? [] as $group) {
            $metadata[$group] = [
                'properties' => [
                        $this->parameter_name ?? '' => [
                        'set' => ['type' => $this->type, 'name' => $this->method_name, 'args' => $this->args]
                    ]
                ]
            ];
        }

        return $metadata;
    }

    /**
     * @throws MissingParameterException
     */
    function validate()
    {
        $this->hasParametersOrThrowException(['method_name', 'parameter_name', 'ref_groups', 'args']);
        return true;
    }
}
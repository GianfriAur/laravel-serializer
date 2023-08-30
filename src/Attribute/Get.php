<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Get extends AbstractSerializeAttribute
{
    public function __construct(
        public string  $property_name,
        public ?string $parameter_name = null,
        public ?array  $ref_groups = null,
        public string  $type = 'direct'
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
                        'get' => ['type' => $this->type, 'property' => $this->property_name],
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
        $this->hasParametersOrThrowException(['property_name', 'parameter_name', 'ref_groups']);
        return true;
    }
}
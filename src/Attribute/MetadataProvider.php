<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class MetadataProvider extends AbstractSerializeAttribute
{
    public function __construct(
        public string  $metadataProviderClass,
        public ?string $parameter_name = null,
        public ?array  $ref_groups = null,
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
                        'metadata_service' => $this->metadataProviderClass
                    ]
                ]
            ];
        }
        return $metadata;
    }

    function validate()
    {
        $this->hasParametersOrThrowException(['metadataProviderClass', 'parameter_name', 'ref_groups']);
        return true;
    }

}
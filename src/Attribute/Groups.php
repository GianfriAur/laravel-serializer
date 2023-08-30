<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Groups extends AbstractSerializeAttribute
{
    public function __construct(
        public array $groups,
        public ?string $parameter_name=null,
        public ?array $ref_groups=null,
    )
    {
        parent::__construct($parameter_name,$ref_groups);
    }
    function injectMetadata(): array
    {
        $metadata = [];

        foreach ($this->ref_groups??[] as $group){
            $metadata[$group]=[
                'properties' =>[
                        $this->parameter_name??''=>[
                        'groups' => $this->groups
                    ]
                ]
            ];
        }
        return $metadata;
    }

    function validate()
    {
        $this->hasParametersOrThrowException(['groups','parameter_name','ref_groups']);
        return true;
    }

}
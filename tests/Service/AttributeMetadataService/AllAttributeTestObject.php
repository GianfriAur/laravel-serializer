<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService;

use Gianfriaur\Serializer\Attribute as GSA;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;

#[GSA\Get(property_name:'get_test', parameter_name: 'get_test', ref_groups:['detail'])]
#[GSA\GetThrough(method_name: 'getBarTest', args: [1, 2, 3], parameter_name: 'get_through_test', ref_groups: ['detail'])]
#[GSA\Groups(groups: ['biz'], parameter_name: 'groups_test', ref_groups:['detail'])]
#[GSA\MetadataProvider(metadataProviderClass: DefaultMetadataService::class, parameter_name: 'metadata_provider_test', ref_groups: ['detail'])]
#[GSA\Name(name:'foo', parameter_name: 'name_test', ref_groups:['detail'])]
#[GSA\Set(property_name:'bar', parameter_name: 'set_test', ref_groups:['detail'])]
#[GSA\SetThrough(method_name: 'setBar', args: [1, 2, 3], parameter_name: 'set_through_test', ref_groups: ['detail'])]

#[GSA\Get(property_name:'get_test', parameter_name: 'all_test', ref_groups:['detail'])]
#[GSA\Groups(groups: ['biz'], parameter_name: 'all_test', ref_groups:['detail'])]
#[GSA\MetadataProvider(metadataProviderClass: DefaultMetadataService::class, parameter_name: 'all_test', ref_groups: ['detail'])]
#[GSA\Name(name:'foo', parameter_name: 'all_test', ref_groups:['detail'])]
#[GSA\Set(property_name:'bar', parameter_name: 'all_test', ref_groups:['detail'])]

#[GSA\Group(name: 'list', parameters: [
    'my_foo' => 'foo',
    'my_bar' => [
        new GSA\Get('bar'),
        new GSA\SetThrough('setBar', [1,2,3]),
        new GSA\Name('my_amazing_bar')
    ]
])]
class AllAttributeTestObject
{

}
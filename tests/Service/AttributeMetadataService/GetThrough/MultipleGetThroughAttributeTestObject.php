<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\GetThrough;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\GetThrough(method_name: 'getBar', parameter_name: 'bar', ref_groups: ['list', 'detail'], args: [1, 2, 3])]
#[GSA\GetThrough(method_name: 'getBarBis', parameter_name: 'bar_bis', ref_groups: ['detail'], args: [1, 2, 3])]
#[GSA\GetThrough(method_name: 'getBarCustom', parameter_name: 'bar_custom', ref_groups: ['detail'], args: [1, 2, 3],type: 'custom')]
class MultipleGetThroughAttributeTestObject
{

}
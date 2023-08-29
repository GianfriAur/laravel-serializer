<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\SetThrough(method_name: 'setBar', parameter_name: 'bar', ref_groups: ['list', 'detail'], args: [1, 2, 3])]
#[GSA\SetThrough(method_name: 'setBarBis', parameter_name: 'bar_bis', ref_groups: ['detail'], args: [1, 2, 3])]
#[GSA\SetThrough(method_name: 'setBarCustom', parameter_name: 'bar_custom', ref_groups: ['detail'], args: [1, 2, 3],type: 'custom')]
class MultipleSetThroughAttributeTestObject
{

}
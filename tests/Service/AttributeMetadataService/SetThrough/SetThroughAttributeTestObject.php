<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\SetThrough(method_name: 'setBar', parameter_name: 'bar', ref_groups: ['list', 'detail'], args: [1, 2, 3])]
class SetThroughAttributeTestObject
{

}
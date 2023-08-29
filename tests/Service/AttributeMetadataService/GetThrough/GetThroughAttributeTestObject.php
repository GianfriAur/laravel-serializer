<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\GetThrough;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\GetThrough(method_name: 'getBar', parameter_name: 'bar', ref_groups: ['list', 'detail'], args: [1, 2, 3])]
class GetThroughAttributeTestObject
{

}
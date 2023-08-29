<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\SetThrough(method_name: 'getBar', parameter_name: 'bar', ref_groups: ['list', 'detail'])]
class BadSetThroughAttributeTestObject
{

}
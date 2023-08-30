<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Group(name:'list',parameters: [ new \stdClass()])]
class BadGroupParameterMalformedAttributeTestObject
{

}
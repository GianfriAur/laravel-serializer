<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Set;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Set(property_name:'bar', parameter_name: 'bar')]
class BadSetAttributeTestObject
{

}
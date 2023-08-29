<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Get;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Get(property_name:'bar', parameter_name: 'bar', ref_groups:['list','detail'])]
class GetAttributeTestObject
{

}
<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Name(name:'foo', parameter_name: 'bar', ref_groups:['list','detail'])]
class NameAttributeTestObject
{

}
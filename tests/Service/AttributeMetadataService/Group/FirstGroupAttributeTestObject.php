<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;
use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Group(name:'list',parameters: [ 'foo' ])]
#[GSA\Group(name:'detail',parameters: [ 'foo', 'bar' ])]
class FirstGroupAttributeTestObject
{

}
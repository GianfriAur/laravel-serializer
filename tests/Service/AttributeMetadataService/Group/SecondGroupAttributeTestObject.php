<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;
use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Group(name:'list',parameters: [ 'my_foo'=>'foo' ])]
#[GSA\Group(name:'detail',parameters: [ 'my_foo'=>'foo', 'my_bar'=>'bar' ])]
class SecondGroupAttributeTestObject
{

}
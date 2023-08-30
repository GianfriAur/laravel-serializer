<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;
use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Get(property_name:'bar', parameter_name: 'my_bar', ref_groups:['detail'])]
#[GSA\Set(property_name:'bar', parameter_name: 'my_bar', ref_groups:['detail'])]

#[GSA\Group(name:'list',parameters: [ 'my_foo'=>'foo' ])]
#[GSA\Group(name:'detail',parameters: [ 'my_foo'=>'foo', 'my_bar'=> new GSA\Name('my_amazing_bar') ])]
class ThirdGroupAttributeTestObject
{

}
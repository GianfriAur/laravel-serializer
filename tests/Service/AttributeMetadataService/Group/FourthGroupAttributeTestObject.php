<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Group(name: 'list', parameters: ['my_foo' => 'foo'])]
#[GSA\Group(name: 'detail', parameters: [
    'my_foo' => 'foo',
    'my_bar' => [
        new GSA\Get('bar'),
        new GSA\SetThrough('setBar', [1,2,3]),
        new GSA\Name('my_amazing_bar')
    ]
])]
class FourthGroupAttributeTestObject
{

}
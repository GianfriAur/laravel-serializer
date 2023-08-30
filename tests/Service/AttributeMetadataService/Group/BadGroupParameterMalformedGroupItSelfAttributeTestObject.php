<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Group(name:'list',parameters: [ 'property' => new GSA\Group('sub_group',[])])]
class BadGroupParameterMalformedGroupItSelfAttributeTestObject
{

}
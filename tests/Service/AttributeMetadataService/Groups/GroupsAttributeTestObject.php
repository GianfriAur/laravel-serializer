<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Groups(['biz'], parameter_name: 'bar', ref_groups:['list','detail'])]
class GroupsAttributeTestObject
{

}
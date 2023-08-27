<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Groups(['biz'], parameter_name: 'bar', ref_groups:['list','detail'])]
#[GSA\Groups(['alpha'], parameter_name: 'bar', ref_groups:['detail'])]
class MultipleGroupsAttributeTestObject
{

}
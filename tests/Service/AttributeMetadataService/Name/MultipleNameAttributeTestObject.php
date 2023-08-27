<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Name(name:'foo', parameter_name: 'bar', ref_groups:['list','detail'])]
#[GSA\Name(name:'foo_bis', parameter_name: 'bar_bis', ref_groups:['detail'])]
class MultipleNameAttributeTestObject
{

}
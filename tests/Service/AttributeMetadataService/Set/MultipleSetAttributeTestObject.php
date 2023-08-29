<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Set;

use Gianfriaur\Serializer\Attribute as GSA;

#[GSA\Set(property_name:'bar', parameter_name: 'bar', ref_groups:['list','detail'])]
#[GSA\Set(property_name:'bar_bis', parameter_name: 'bar_bis', ref_groups:['detail'])]
#[GSA\Set(property_name:'bar_custom', parameter_name: 'bar_custom', ref_groups:['detail'] ,type:'custom')]
class MultipleSetAttributeTestObject
{

}
<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\MetadataProvider;

use Gianfriaur\Serializer\Attribute as GSA;
use Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;

#[GSA\MetadataProvider(metadataProviderClass: DefaultMetadataService::class, parameter_name: 'bar', ref_groups: ['list', 'detail'])]
#[GSA\MetadataProvider(metadataProviderClass: AttributeMetadataService::class, parameter_name: 'bar_bis', ref_groups: ['detail'])]
class MultipleMetadataProviderTestObject
{

}
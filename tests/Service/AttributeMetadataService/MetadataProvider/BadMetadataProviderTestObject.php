<?php

namespace Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\MetadataProvider;

use Gianfriaur\Serializer\Attribute as GSA;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;

#[GSA\MetadataProvider(metadataProviderClass: DefaultMetadataService::class, ref_groups: ['list', 'detail'])]
class BadMetadataProviderTestObject
{

}
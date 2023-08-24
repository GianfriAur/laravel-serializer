<?php

namespace Gianfriaur\Serializer\Tests\Service;

use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;
use Gianfriaur\Serializer\Service\Serializer\DefaultSerializer;
use Gianfriaur\Serializer\Tests\Service\DefaultMetadataServiceTest\CustomObject;

class DefaultMetadataServiceTest extends \Orchestra\Testbench\TestCase
{
    /** @test */
    public function test_basic_test()
    {
        $this->assertTrue(true);
    }

    private function log($var)
    {
        $this->expectOutputString(''); // tell PHPUnit to expect '' as output
        dd( $var);
    }


    private function getNewDefaultMetadataService(): DefaultMetadataService
    {
        return new DefaultMetadataService();
    }

    public function getExpectedProperties(): array
    {
        return [
            'name' => [
                'get' => ['type' => 'direct', 'property' => 'property'],
                'set' => ['type' => 'direct', 'property' => 'property'],
                'name' => 'first_name',
                'groups' => ['g1', 'g2'],
                'default' => '',
                'metadata_service' => DefaultMetadataService::class
            ]
        ];
    }

    public function getExpectedPropertiesBis(): array
    {
        return [
            'surname' => [
                'get' => ['type' => 'direct', 'property' => 'property'],
                'set' => ['type' => 'direct', 'property' => 'property'],
                'name' => 'first_name',
                'groups' => ['g1', 'g2'],
                'default' => '',
                'metadata_service' => DefaultMetadataService::class
            ]
        ];
    }

    public function test_addGroup_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());

        $this->assertEquals($ms->hasSerializationMetadata(CustomObject::class, ['base']), true);
    }

    public function test_addGroup_fail()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());

        $this->assertEquals($ms->hasSerializationMetadata(CustomObject::class, ['not_exist']), false);
    }

    public function test_addGroup_ok_and_fail()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());

        $this->assertEquals($ms->hasSerializationMetadata(CustomObject::class, ['base', 'not_exist']), true);
    }

    public function test_addGroup_validate_metadata()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addGroup(CustomObject::class, 'base_bis', $this->getExpectedPropertiesBis());

        // $this->log($ms->getSerializationMetadata(CustomObject::class, ['bases']));

        $this->assertEquals($ms->getSerializationMetadata(CustomObject::class, ['base']), [
            'properties' => $this->getExpectedProperties(),
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
        $this->assertEquals($ms->getSerializationMetadata(CustomObject::class, ['base_bis']), [
            'properties' => $this->getExpectedPropertiesBis(),
            'metadata_service' => $ms::class,
            'groups' => ['base_bis']
        ]);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base', 'base_bis']);

        $this->assertEquals(
            isset($metadata['properties']['name']) && isset($metadata['properties']['surname']),
            true
        );
    }

    public function test_addProperty_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addProperty(CustomObject::class, 'base', 'my_prop', [
            'get' => ['type' => 'direct', 'property' => 'my_prop'],
            'name' => 'my_prop',
        ]);
        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'get' => ['type' => 'direct', 'property' => 'my_prop'],
                    'name' => 'my_prop',
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addProperty_override()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addProperty(CustomObject::class, 'base', 'name', [
            'get' => ['type' => 'direct', 'property' => 'my_prop'],
            'name' => 'my_prop',
        ]);
        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                'name' => [
                    'get' => ['type' => 'direct', 'property' => 'my_prop'],
                    'name' => 'my_prop',
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyGetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyGetter(CustomObject::class, 'base', 'my_prop', ['type' => 'direct', 'property' => 'my_prop']);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'get' => ['type' => 'direct', 'property' => 'my_prop']
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyDirectGetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyDirectGetter(CustomObject::class, 'base', 'my_prop', 'my_over');

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'get' => ['type' => 'direct', 'property' => 'my_over']
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyFunctionGetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyFunctionGetter(CustomObject::class, 'base', 'my_prop', 'getMyProp', [1, 2, 3]);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'get' => ['type' => 'function', 'name' => 'getMyProp', 'args' => [1, 2, 3]]
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertySetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertySetter(CustomObject::class, 'base', 'my_prop', ['type' => 'direct', 'property' => 'my_prop']);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'set' => ['type' => 'direct', 'property' => 'my_prop']
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyDirectSetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyDirectsetter(CustomObject::class, 'base', 'my_prop', 'my_over');

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'set' => ['type' => 'direct', 'property' => 'my_over']
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyFunctionSetter_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyFunctionSetter(CustomObject::class, 'base', 'my_prop', 'getMyProp', [1, 2, 3]);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'set' => ['type' => 'function', 'name' => 'getMyProp', 'args' => [1, 2, 3]]
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertySerializedName_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertySerializedName(CustomObject::class, 'base', 'my_prop', 'my_name_override');

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'name' => 'my_name_override'
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyGroups_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyGroups(CustomObject::class, 'base', 'my_prop', ['g1','g2']);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'groups' => ['g1','g2']
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

    public function test_addPropertyDefaultValue_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyDefaultValue(CustomObject::class, 'base', 'my_prop', 'default_value');

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'default' =>'default_value'
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }


    public function test_addPropertyMetadataServiceProvider_ok()
    {
        $ms = $this->getNewDefaultMetadataService();

        $ms->addGroup(CustomObject::class, 'base', $this->getExpectedProperties());
        $ms->addPropertyMetadataServiceProvider(CustomObject::class, 'base', 'my_prop', \stdClass::class);

        $metadata = $ms->getSerializationMetadata(CustomObject::class, ['base']);

        $this->assertEquals($metadata, [
            'properties' => [
                ...$this->getExpectedProperties(),
                'my_prop' => [
                    'metadata_service' => 'stdClass'
                ]
            ],
            'metadata_service' => $ms::class,
            'groups' => ['base']
        ]);
    }

}
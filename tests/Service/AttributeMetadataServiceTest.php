<?php

namespace Gianfriaur\Serializer\Tests\Service;

use Gianfriaur\Serializer\Exception\Attribute\GroupAttributeMalformed;
use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;
use Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\AllAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Get\BadGetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Get\GetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Get\MultipleGetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\GetThrough\BadGetThroughAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\GetThrough\GetThroughAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\GetThrough\MultipleGetThroughAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\BadGroupParameterMalformedAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\BadGroupParameterMalformedGroupItSelfAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\BadGroupParameterMalformedSubGroupItSelfAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\BadGroupParameterMalformedSubGroupNotAbstractSerializeAttributeAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\FirstGroupAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\FourthGroupAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\SecondGroupAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Group\ThirdGroupAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\BadGroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\GroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\MultipleGroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\MetadataProvider\BadMetadataProviderTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\MetadataProvider\MetadataProviderTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\MetadataProvider\MultipleMetadataProviderTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\BadNameAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\MultipleNameAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\NameAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Set\BadSetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Set\MultipleSetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Set\SetAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough\BadSetThroughAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough\MultipleSetThroughAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\SetThrough\SetThroughAttributeTestObject;

class AttributeMetadataServiceTest extends \Orchestra\Testbench\TestCase
{
    /** @test */
    public function test_basic_test()
    {
        $this->assertTrue(true);
    }

    private function log($var)
    {
        $this->expectOutputString('');
        dd($var);
    }


    private function getNewAttributeMetadataService(): AttributeMetadataService
    {
        return new AttributeMetadataService([]);
    }

    public function test_NameAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(NameAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(NameAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(NameAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["name" => "foo"]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleNameAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleNameAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleNameAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleNameAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["name" => "foo"]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["name" => "foo"], "bar_bis" => ["name" => "foo_bis"]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadNameAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'Name' the parameter 'ref_groups' is missing, to solve it add: #[Name( ... , ref_groups: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadNameAttributeTestObject::class, ['list']);
    }

    public function test_GroupsAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(GroupsAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(GroupsAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(GroupsAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["groups" => ['biz']]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleGroupsAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleGroupsAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleGroupsAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleGroupsAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["groups" => ['biz']]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["groups" => ['biz', 'alpha']]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadGroupsAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'Groups' the parameter 'parameter_name' is missing, to solve it add: #[Groups( ... , parameter_name: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadGroupsAttributeTestObject::class, ['list']);
    }

    public function test_MetadataProviderTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MetadataProviderTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MetadataProviderTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MetadataProviderTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["metadata_service" => DefaultMetadataService::class]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleMetadataProviderTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleMetadataProviderTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleMetadataProviderTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleMetadataProviderTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["metadata_service" => DefaultMetadataService::class]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "bar" => ["metadata_service" => DefaultMetadataService::class],
                "bar_bis" => ["metadata_service" => AttributeMetadataService::class]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadMetadataProviderTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'MetadataProvider' the parameter 'parameter_name' is missing, to solve it add: #[MetadataProvider( ... , parameter_name: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadMetadataProviderTestObject::class, ['list']);
    }

    public function test_GetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(GetAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(GetAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(GetAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["get" => ['type' => 'direct', 'property' => 'bar']]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleGetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleGetAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleGetAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleGetAttributeTestObject::class, ['other']);


        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["get" => ['type' => 'direct', 'property' => 'bar']]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "bar" => ["get" => ['type' => 'direct', 'property' => 'bar']],
                "bar_bis" => ["get" => ['type' => 'direct', 'property' => 'bar_bis']],
                "bar_custom" => ["get" => ['type' => 'custom', 'property' => 'bar_custom']]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadGetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'Get' the parameter 'ref_groups' is missing, to solve it add: #[Get( ... , ref_groups: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadGetAttributeTestObject::class, ['list']);
    }

    public function test_GetThroughAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(GetThroughAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(GetThroughAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(GetThroughAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["get" => ['type' => 'function', 'name' => 'getBar', 'args' => [1, 2, 3]]]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleGetThroughAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleGetThroughAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleGetThroughAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleGetThroughAttributeTestObject::class, ['other']);


        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["get" => ['type' => 'function', 'name' => 'getBar', 'args' => [1, 2, 3]]]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "bar" => ["get" => ['type' => 'function', 'name' => 'getBar', 'args' => [1, 2, 3]]],
                "bar_bis" => ["get" => ['type' => 'function', 'name' => 'getBarBis', 'args' => [1, 2, 3]]],
                "bar_custom" => ["get" => ['type' => 'custom', 'name' => 'getBarCustom', 'args' => [1, 2, 3]]],
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadGetThroughAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'GetThrough' the parameter 'args' is missing, to solve it add: #[GetThrough( ... , args: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadGetThroughAttributeTestObject::class, ['list']);
    }

    public function test_SetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(SetAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(SetAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(SetAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["set" => ['type' => 'direct', 'property' => 'bar']]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleSetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleSetAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleSetAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleSetAttributeTestObject::class, ['other']);


        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["set" => ['type' => 'direct', 'property' => 'bar']]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "bar" => ["set" => ['type' => 'direct', 'property' => 'bar']],
                "bar_bis" => ["set" => ['type' => 'direct', 'property' => 'bar_bis']],
                "bar_custom" => ["set" => ['type' => 'custom', 'property' => 'bar_custom']]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadSetAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'Set' the parameter 'ref_groups' is missing, to solve it add: #[Set( ... , ref_groups: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadSetAttributeTestObject::class, ['list']);
    }

    public function test_SetThroughAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(SetThroughAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(SetThroughAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(SetThroughAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_all = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]]]]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_all, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_MultipleSetThroughAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(MultipleSetThroughAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(MultipleSetThroughAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(MultipleSetThroughAttributeTestObject::class, ['other']);


        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => ["bar" => ["set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]]]]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "bar" => ["set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]]],
                "bar_bis" => ["set" => ['type' => 'function', 'name' => 'setBarBis', 'args' => [1, 2, 3]]],
                "bar_custom" => ["set" => ['type' => 'custom', 'name' => 'setBarCustom', 'args' => [1, 2, 3]]],
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_BadSetThroughAttributeTestObjectAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(MissingParameterException::class);
        $this->expectExceptionMessage("In the attribute 'SetThrough' the parameter 'args' is missing, to solve it add: #[SetThrough( ... , args: MY_VALUE )]");

        $metadata_list = $ms->getSerializationMetadata(BadSetThroughAttributeTestObject::class, ['list']);
    }

    public function test_BadGroupParameterMalformedAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(GroupAttributeMalformed::class);
        $this->expectExceptionMessage('In the attribute \'Group\' the parameter \'parameters\' can only contain string or array es:  #[Group( ... , parameters: [ \'attr\', \'attr_2\' => [] ] )]');

        $metadata_list = $ms->getSerializationMetadata(BadGroupParameterMalformedAttributeTestObject::class, ['list']);
    }

    public function test_BadGroupParameterMalformedGroupItSelfAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(GroupAttributeMalformed::class);
        $this->expectExceptionMessage('In the attribute \'Group\' the parameter \'parameters\' can\'t contains it self');

        $metadata_list = $ms->getSerializationMetadata(BadGroupParameterMalformedGroupItSelfAttributeTestObject::class, ['list']);
    }

    public function test_BadGroupParameterMalformedSubGroupItSelfAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(GroupAttributeMalformed::class);
        $this->expectExceptionMessage('In the attribute \'Group\' the parameter \'parameters\' can\'t contains it self');

        $metadata_list = $ms->getSerializationMetadata(BadGroupParameterMalformedSubGroupItSelfAttributeTestObject::class, ['list']);
    }

    public function test_BadGroupParameterMalformedSubGroupNotAbstractSerializeAttributeAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $this->expectException(GroupAttributeMalformed::class);
        $this->expectExceptionMessage('In the attribute \'Group\' the parameter \'parameters\' can contain only AbstractSerializeAttribute es:  #[Group( ... , parameters: [ \'attr\', \'attr_2\' => [ new Name( ... ) ] ]');

        $metadata_list = $ms->getSerializationMetadata(BadGroupParameterMalformedSubGroupNotAbstractSerializeAttributeAttributeTestObject::class, ['list']);
    }

    public function test_FirstGroupAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(FirstGroupAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(FirstGroupAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(FirstGroupAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => [
                "foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ]
            ]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ],
                "bar" => [
                    "set" => ['type' => 'direct', 'property' => 'bar'],
                    "get" => ['type' => 'direct', 'property' => 'bar'],
                    "name" => 'bar'
                ]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_SecondGroupAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(SecondGroupAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(SecondGroupAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(SecondGroupAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ]
            ]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ],
                "my_bar" => [
                    "set" => ['type' => 'direct', 'property' => 'bar'],
                    "get" => ['type' => 'direct', 'property' => 'bar'],
                    "name" => 'bar'
                ]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_ThirdGroupAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(ThirdGroupAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(ThirdGroupAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(ThirdGroupAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ]
            ]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ],
                "my_bar" => [
                    "set" => ['type' => 'direct', 'property' => 'bar'],
                    "get" => ['type' => 'direct', 'property' => 'bar'],
                    "name" => 'my_amazing_bar'
                ]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_FourthGroupAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(FourthGroupAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(FourthGroupAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(FourthGroupAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ]
            ]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ],
                "my_bar" => [
                    "set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]],
                    "get" => ['type' => 'direct', 'property' => 'bar'],
                    "name" => 'my_amazing_bar'
                ]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }

    public function test_AllAttributeTestObject_ok()
    {
        $ms = $this->getNewAttributeMetadataService();

        $metadata_list = $ms->getSerializationMetadata(AllAttributeTestObject::class, ['list']);
        $metadata_detail = $ms->getSerializationMetadata(AllAttributeTestObject::class, ['detail']);
        $metadata_other = $ms->getSerializationMetadata(AllAttributeTestObject::class, ['other']);

        $expected_metadata_base = [
            "metadata_service" => "Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService"
        ];

        $expected_metadata_list = [...$expected_metadata_base, ... [
            "properties" => [
                "my_foo" => [
                    "set" => ['type' => 'direct', 'property' => 'foo'],
                    "get" => ['type' => 'direct', 'property' => 'foo'],
                    "name" => 'foo'
                ],
                "my_bar" => [
                    "set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]],
                    "get" => ['type' => 'direct', 'property' => 'bar'],
                    "name" => 'my_amazing_bar'
                ]
            ]
        ]];

        $expected_metadata_detail = [...$expected_metadata_base, ... [
            "properties" => [
                "get_test" => ["get" => ['type' => 'direct', 'property' => 'get_test']],
                "get_through_test" => ["get" => ['type' => 'function', 'name' => 'getBarTest', 'args' => [1, 2, 3]]],
                "groups_test" => ["groups" => ['biz']],
                "metadata_provider_test" => ["metadata_service" => DefaultMetadataService::class],
                "name_test" => ["name" => 'foo'],
                "set_test" => ["set" => ['type' => 'direct', 'property' => 'bar']],
                "set_through_test" => ["set" => ['type' => 'function', 'name' => 'setBar', 'args' => [1, 2, 3]]],
                "all_test" => [
                    "get" => ['type' => 'direct', 'property' => 'get_test'],
                    "groups" => ['biz'],
                    "metadata_service" => DefaultMetadataService::class,
                    "name" => 'foo',
                    "set" => ['type' => 'direct', 'property' => 'bar']
                ]
            ]
        ]];

        $expected_metadata_other = [...$expected_metadata_base, ... [
            "properties" => []
        ]];

        $this->assertEquals([...$expected_metadata_list, ...["groups" => ["list"]]], $metadata_list);
        $this->assertEquals([...$expected_metadata_detail, ...["groups" => ["detail"]]], $metadata_detail);
        $this->assertEquals([...$expected_metadata_other, ...["groups" => ["other"]]], $metadata_other);
    }
}//
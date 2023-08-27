<?php

namespace Gianfriaur\Serializer\Tests\Service;

use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;
use Gianfriaur\Serializer\Service\MetadataService\AttributeMetadataService;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\BadGroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\GroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Groups\MultipleGroupsAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\BadNameAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\MultipleNameAttributeTestObject;
use Gianfriaur\Serializer\Tests\Service\AttributeMetadataService\Name\NameAttributeTestObject;

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

        $expected_metadata_all = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["name" => "foo"]]
        ]];

        $expected_metadata_other = [ ...$expected_metadata_base,... [
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

        $expected_metadata_list = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["name" => "foo"]]
        ]];

        $expected_metadata_detail = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["name" => "foo"], "bar_bis" => ["name"=> "foo_bis"]]
        ]];

        $expected_metadata_other = [ ...$expected_metadata_base,... [
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

        $expected_metadata_all = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["groups" => ['biz']]]
        ]];

        $expected_metadata_other = [ ...$expected_metadata_base,... [
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

        $expected_metadata_list = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["groups" => ['biz']]]
        ]];

        $expected_metadata_detail = [ ...$expected_metadata_base,... [
            "properties" => ["bar" => ["groups" => ['biz','alpha']]]
        ]];

        $expected_metadata_other = [ ...$expected_metadata_base,... [
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

}
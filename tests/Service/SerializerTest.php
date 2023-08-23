<?php

namespace Gianfriaur\Serializer\Tests\Service;

use Gianfriaur\Serializer\Exception\EngineAlreadyRegisteredException;
use Gianfriaur\Serializer\Exception\MissingDefaultEngineException;
use Gianfriaur\Serializer\Exception\MissingEngineException;
use Gianfriaur\Serializer\Exception\MissingMetadataServiceException;
use Gianfriaur\Serializer\Service\Engine\ArrayEngine;
use Gianfriaur\Serializer\Service\Engine\JsonEngine;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;
use Gianfriaur\Serializer\Service\Serializer\DefaultSerializer;
use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;

class SerializerTest extends \Orchestra\Testbench\TestCase
{

    /** @test */
    public function test_basic_test()
    {
        $this->assertTrue(true);
    }

    private function log($var)
    {
        $this->expectOutputString(''); // tell PHPUnit to expect '' as output
        print_r("Hello World");
        print "Ping";
        echo "Pong";
        $out = "Foo";
        dd("\n", $var);
    }

    private function getNewSerializer(): DefaultSerializer
    {
        return new DefaultSerializer($this->app, []);
    }

    public function test_constructor()
    {
        $serializer = $this->getNewSerializer();
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    public function test_setDefaultEngine_missing_engine()
    {
        $serializer = $this->getNewSerializer();
        $this->expectException(MissingEngineException::class);
        $this->expectExceptionMessage('Engine named array is missing');
        $serializer->setDefaultEngine(new ArrayEngine($this->app, $serializer));
        $this->expectOutputString('');
    }

    public function test_setDefaultEngine_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $serializer->setDefaultEngine($engine);
        $this->assertEquals($serializer->getDefaultEngine(), $engine);
    }

    public function test_getDefaultEngine_missing_default_engine()
    {
        $serializer = $this->getNewSerializer();
        $this->expectException(MissingDefaultEngineException::class);
        $this->expectExceptionMessage('In the serialization service the default serializer is missing');
        $serializer->getDefaultEngine();
    }

    public function test_getDefaultEngine_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $serializer->setDefaultEngine($engine);
        $this->assertEquals($serializer->getDefaultEngine(), $engine);
    }

    public function test_setDefaultEngineName_missing_engine()
    {
        $serializer = $this->getNewSerializer();
        $this->expectException(MissingEngineException::class);
        $this->expectExceptionMessage('Engine named array is missing');
        $serializer->setDefaultEngineName('array');
    }

    public function test_setDefaultEngineName_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $serializer->setDefaultEngineName('array');
        $this->assertEquals($serializer->getDefaultEngine(), $engine);
    }

    public function test_getDefaultEngineName_missing_default_engine()
    {
        $serializer = $this->getNewSerializer();
        $this->expectException(MissingDefaultEngineException::class);
        $this->expectExceptionMessage('In the serialization service the default serializer is missing');
        $serializer->getDefaultEngineName();
    }

    public function test_getDefaultEngineName_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $serializer->setDefaultEngine($engine);
        $this->assertEquals($serializer->getDefaultEngineName(), 'array');
    }

    public function test_addEngine_engine_already_registered()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);

        $this->expectException(EngineAlreadyRegisteredException::class);
        $this->expectExceptionMessage('Engine named array already registered');

        $serializer->addEngine($engine);

    }

    public function test_addEngine_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $this->assertEquals($serializer->getEngines(), ['array' => $engine]);
    }

    public function test_addEngine_multiple()
    {
        $serializer = $this->getNewSerializer();
        $engine_array = new ArrayEngine($this->app, $serializer);
        $engine_json = new JsonEngine($this->app, $serializer);
        $serializer->addEngine($engine_array);
        $serializer->addEngine($engine_json);
        $this->assertEquals($serializer->getEngines(), ['array' => $engine_array, 'json' => $engine_json]);
    }

    public function test_removeEngine_missing_engine()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);

        $this->expectException(MissingEngineException::class);
        $this->expectExceptionMessage('Engine named array is missing');

        $serializer->removeEngine($engine);
    }

    public function test_removeEngine_OK()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $this->assertEquals($serializer->getEngines(), ['array' => $engine]);
        $serializer->removeEngine($engine);
        $this->assertEquals($serializer->getEngines(), []);
    }

    public function test_removeEngine_multiple()
    {
        $serializer = $this->getNewSerializer();
        $engine_array = new ArrayEngine($this->app, $serializer);
        $engine_json = new JsonEngine($this->app, $serializer);
        $serializer->addEngine($engine_array);
        $this->assertEquals($serializer->getEngines(), ['array' => $engine_array]);
        $serializer->addEngine($engine_json);
        $this->assertEquals($serializer->getEngines(), ['array' => $engine_array, 'json' => $engine_json]);
        $serializer->removeEngine($engine_array);
        $this->assertEquals($serializer->getEngines(), ['json' => $engine_json]);
        $serializer->removeEngine($engine_json);
        $this->assertEquals($serializer->getEngines(), []);
    }


    public function test_getEngines_ok()
    {
        $this->test_removeEngine_multiple();
    }

    public function test_hasEngine()
    {
        $serializer = $this->getNewSerializer();
        $this->assertEquals($serializer->hasEngine('array'), false);
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $this->assertEquals($serializer->hasEngine('array'), true);
    }

    public function test_getEngineByName()
    {
        $serializer = $this->getNewSerializer();
        $this->assertEquals($serializer->getEngineByName('array'), null);
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $this->assertEquals($serializer->getEngineByName('array'), $engine);
    }

    public function test_getEngineByNameOrFail_ok()
    {
        $serializer = $this->getNewSerializer();
        $engine = new ArrayEngine($this->app, $serializer);
        $serializer->addEngine($engine);
        $this->assertEquals($serializer->getEngineByNameOrFail('array'), $engine);
    }

    public function test_getEngineByNameOrFail_missing_engine()
    {
        $serializer = $this->getNewSerializer();

        $this->expectException(MissingEngineException::class);
        $this->expectExceptionMessage('Engine named array is missing');

        $engine = $serializer->getEngineByNameOrFail('array');
    }

    public function test_addMetadataService_ok()
    {
        $serializer = $this->getNewSerializer();
        $serializer->addMetadataService(new DefaultMetadataService());

        $this->assertEquals($serializer->hasMetadataService(DefaultMetadataService::class), true);
    }

    public function test_addMetadataService_fail()
    {
        $serializer = $this->getNewSerializer();
        $this->assertEquals($serializer->hasMetadataService(DefaultMetadataService::class), false);
    }

    public function test_hasMetadataService_ok()
    {
        $serializer = $this->getNewSerializer();
        $serializer->addMetadataService(new DefaultMetadataService());

        $this->assertEquals($serializer->hasMetadataService(DefaultMetadataService::class), true);
    }

    public function test_hasMetadataService_null()
    {
        $serializer = $this->getNewSerializer();
        $this->assertEquals($serializer->hasMetadataService(DefaultMetadataService::class), false);
    }

    public function test_getMetadataService_ok()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $serializer->addMetadataService($metadata_service);

        $this->assertEquals($serializer->getMetadataService($metadata_service::class), $metadata_service);
    }

    public function test_getMetadataService_fail()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $this->assertEquals($serializer->getMetadataService($metadata_service::class), null);
    }

    public function test_getMetadataServiceOrFail_ok()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $serializer->addMetadataService($metadata_service);

        $this->assertEquals($serializer->getMetadataServiceOrFail($metadata_service::class), $metadata_service);
    }


    public function test_getMetadataServiceOrFail_missing_metadata_service()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $this->expectException(MissingMetadataServiceException::class);
        $this->expectExceptionMessage('MetadataService named ' . $metadata_service::class . ' is missing');

        $this->assertEquals($serializer->getMetadataServiceOrFail($metadata_service::class), $metadata_service);
    }

    public function test_getMetadataServices_empty()
    {
        $serializer = $this->getNewSerializer();
        $this->assertEquals($serializer->getMetadataServices(), []);
    }


    public function test_getMetadataServices_ok()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $serializer->addMetadataService($metadata_service);

        $this->assertEquals($serializer->getMetadataServices(), [$metadata_service::class => $metadata_service]);
    }

    public function test_removeMetadataService_ok()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $serializer->addMetadataService($metadata_service);

        $this->assertEquals($serializer->getMetadataServices(), [$metadata_service::class => $metadata_service]);

        $serializer->removeMetadataService($metadata_service);

        $this->assertEquals($serializer->getMetadataServices(), []);
    }

    public function test_removeMetadataService_missing_metadata_service()
    {
        $serializer = $this->getNewSerializer();

        $metadata_service = new DefaultMetadataService();

        $this->expectException(MissingMetadataServiceException::class);
        $this->expectExceptionMessage('MetadataService named ' . $metadata_service::class . ' is missing');

        $serializer->removeMetadataService($metadata_service);
    }




    /**
     * TODO: add test for serialize, getObjectSerializationMetadata
     */

    // $this->log($serializer->getDefaultEngine());
}
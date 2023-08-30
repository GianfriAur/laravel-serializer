<?php

namespace Gianfriaur\Serializer\Service\Serializer;

use ArrayAccess;
use Countable;
use Gianfriaur\Serializer\Exception\EngineAlreadyRegisteredException;
use Gianfriaur\Serializer\Exception\MetadataServiceAlreadyRegisteredException;
use Gianfriaur\Serializer\Exception\MissingDefaultEngineException;
use Gianfriaur\Serializer\Exception\MissingEngineException;
use Gianfriaur\Serializer\Exception\MissingMetadataServiceException;
use Gianfriaur\Serializer\Exception\RecursiveSerializationException;
use Gianfriaur\Serializer\Exception\SerializePrimitiveException;
use Gianfriaur\Serializer\Service\Engine\EngineInterface;
use Gianfriaur\Serializer\Service\MetadataService\MetadataServiceInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class DefaultSerializer implements SerializerInterface
{
    /** @var array<EngineInterface> store all registered engines */
    private array $engines;
    /** @var string store the default engine name */
    private string $default_engine_name;

    /** @var array<MetadataServiceInterface> store all registered engines */
    private array $metadata_services;


    /** @noinspection PhpPropertyOnlyWrittenInspection
     * @param Application $app
     * @param array{
     *     serialize_null_as_null: bool,
     *     serialize_empty_as_null: bool,
     *     serialize_primitive: bool,
     *     serialize_null: bool,
     *     prevent_recursive_serialization: bool
     * } $options
     */
    public function __construct(
        private readonly Application $app,
        private readonly array       $options
    )
    {
        $this->engines = [];
        $this->metadata_services = [];
        $this->default_engine_name = '';
    }

    private function getOptions(): array
    {
        return [
            ...[
                'serialize_null_as_null' => true,
                'serialize_empty_as_null' => true,
                'serialize_primitive' => false,
                'serialize_null' => false,
                'prevent_recursive_serialization' => false,
            ],
            ...$this->options
        ];
    }

    /**
     * set default engine
     * @throws MissingEngineException
     */
    public function setDefaultEngine(EngineInterface $engine): void
    {
        $this->setDefaultEngineName($engine->getEngineName());
    }

    /**
     * get sefault engine
     * @throws MissingDefaultEngineException
     */
    public function getDefaultEngine(): EngineInterface
    {
        $engine = $this->getEngineByName($this->default_engine_name);
        if (!$engine) {
            throw new MissingDefaultEngineException();
        }
        return $engine;
    }

    /**
     * set default engine by name
     * @throws MissingEngineException
     */
    public function setDefaultEngineName(string $name): void
    {
        $this->default_engine_name = $this->getEngineByNameOrFail($name)->getEngineName();
    }

    /**
     * get default engine name
     * @return string
     * @throws MissingDefaultEngineException
     */
    public function getDefaultEngineName(): string
    {
        return $this->getDefaultEngine()->getEngineName();
    }

    /**
     * add engine
     * @throws EngineAlreadyRegisteredException
     */
    public function addEngine(EngineInterface $engine): void
    {
        if ($this->hasEngine($engine->getEngineName())) {
            throw new EngineAlreadyRegisteredException($engine->getEngineName());
        }
        $this->engines[$engine->getEngineName()] = $engine;
    }

    /**
     * remove engine
     * @throws MissingEngineException
     */
    public function removeEngine(EngineInterface $engine): void
    {
        if (!$this->hasEngine($engine->getEngineName())) {
            throw new MissingEngineException($engine->getEngineName());
        }
        unset($this->engines[$engine->getEngineName()]);
    }

    /**
     * get all engined
     * @return array<string,EngineInterface>
     */
    public function getEngines(): array
    {
        return $this->engines;
    }

    /**
     * check if engine exist
     * @param string $name
     * @return bool
     */
    public function hasEngine(string $name): bool
    {
        return isset($this->getEngines()[$name]);
    }

    /**
     * get engine form name
     * @param string $name
     * @return EngineInterface|null
     */
    public function getEngineByName(string $name): ?EngineInterface
    {
        if (!isset($this->getEngines()[$name])) return null;
        return $this->getEngines()[$name];
    }


    /**
     * get engine form name or fail
     * @throws MissingEngineException
     */
    public function getEngineByNameOrFail(string $name): EngineInterface
    {
        $engine = $this->getEngineByName($name);
        if (!$engine) {
            throw new MissingEngineException($name);
        }
        return $engine;
    }

    /**
     * add metadata service
     * @throws MetadataServiceAlreadyRegisteredException
     */
    public function addMetadataService(MetadataServiceInterface $metadataService): void
    {
        if ($this->hasMetadataService($metadataService::class)) {
            throw new MetadataServiceAlreadyRegisteredException($metadataService::class);
        }
        $this->metadata_services[$metadataService::class] = $metadataService;
    }

    /**
     * remove metadata service
     * @throws MissingMetadataServiceException
     */
    public function removeMetadataService(MetadataServiceInterface $metadataService): void
    {
        $metadataService = $this->getMetadataServiceOrFail($metadataService::class);
        unset($this->metadata_services[$metadataService::class]);
    }

    /**
     * get all metadata services
     * @return array<class-string,MetadataServiceInterface>
     */
    public function getMetadataServices(): array
    {
        return $this->metadata_services;
    }

    /**
     * checha if metadata service exist
     * @param string $className
     * @return bool
     */
    public function hasMetadataService(string $className): bool
    {
        return isset($this->metadata_services[$className]);
    }


    /**
     * get metadata service from class name
     * @param string $className
     * @return MetadataServiceInterface|null
     */
    public function getMetadataService(string $className): MetadataServiceInterface|null
    {
        if ($this->hasMetadataService($className)) {
            return $this->metadata_services[$className];
        }
        return null;
    }

    /**
     * get metadata service from class name or fail
     * @throws MissingMetadataServiceException
     */
    public function getMetadataServiceOrFail(string $className): MetadataServiceInterface
    {
        $metadata_services = $this->getMetadataService($className);
        if (!$metadata_services) {
            throw new MissingMetadataServiceException($className);
        }
        return $metadata_services;
    }

    /**
     * serialize your object
     * @param mixed $object
     * @param array|string $group
     * @param string|null $engine
     * @param string|null $metadataProviderClass
     * @return mixed
     * @throws MissingDefaultEngineException
     * @throws SerializePrimitiveException
     */
    public function serialize(mixed $object, array|string $group, ?string $engine = null, ?string $metadataProviderClass = null,?array $serializationStack=[]): mixed
    {
        $groups = is_array($group) ? $group : [$group];

        $engine = $this->getEngineByName($engine ?? $this->getDefaultEngineName());

        return $engine->serializeObject($object, $groups,$metadataProviderClass, $serializationStack, $this->getOptions());

    }

    /**
     * get SerializationMetadata for your object
     * @param mixed $object
     * @param array $groups
     * @param string|null $metadataProviderClass
     * @return mixed
     */
    public function getObjectSerializationMetadata(mixed $object, array $groups, ?string $metadataProviderClass): mixed
    {
        if ($object === null) return null;
        $metadataProviders = $metadataProviderClass
            ? [$this->getMetadataService($metadataProviderClass)]
            : $this->getMetadataServices();

        foreach ($metadataProviders as $metadataProvider) {
            if ($metadataProvider->hasSerializationMetadata($object::class, $groups)) {
                return $metadataProvider->getSerializationMetadata($object::class, $groups);
            }
        }

        return null;
    }

}
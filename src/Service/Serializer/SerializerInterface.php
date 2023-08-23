<?php

namespace Gianfriaur\Serializer\Service\Serializer;

use Gianfriaur\Serializer\Service\Engine\EngineInterface;
use Gianfriaur\Serializer\Service\MetadataService\MetadataServiceInterface;

interface SerializerInterface
{

    public function setDefaultEngine(EngineInterface $engine);

    public function getDefaultEngine(): EngineInterface;

    public function setDefaultEngineName(string $name);

    public function getDefaultEngineName(): string;

    public function addEngine(EngineInterface $engine);

    public function removeEngine(EngineInterface $engine);

    /**
     * Get all engines
     * @return array<string,EngineInterface>
     */
    public function getEngines(): array;

    /**
     * Check if engine with name exist
     * @param string $name
     * @return bool
     */
    public function hasEngine(string $name): bool;

    /**
     * Get engine if exist
     * @param string $name
     * @return EngineInterface|null
     */
    public function getEngineByName(string $name): EngineInterface|null;

    /**
     * Get engine
     * @param string $name
     * @return EngineInterface
     */
    public function getEngineByNameOrFail(string $name): EngineInterface;

    public function addMetadataService(MetadataServiceInterface $metadataService);

    public function removeMetadataService(MetadataServiceInterface $metadataService);

    /**
     * @return array<class-string,MetadataServiceInterface>
     */
    public function getMetadataServices(): array;

    /**
     * @param class-string<MetadataServiceInterface> $className
     * @return bool
     */
    public function hasMetadataService(string $className): bool;

    /**
     * Get engine if exist
     * @param $className $name
     * @return MetadataServiceInterface|null
     */
    public function getMetadataService(string $className): MetadataServiceInterface|null;

    /**
     * Get engine
     * @param $className $name
     * @return MetadataServiceInterface
     */
    public function getMetadataServiceOrFail(string $className): MetadataServiceInterface;


    public function serialize(mixed $object, array|string $group, ?string $engine = '', ?string $metadataProviderClass = ''): mixed;

    public function getObjectSerializationMetadata(mixed $object,array $groups, ?string $metadataProviderClass ): mixed;

}
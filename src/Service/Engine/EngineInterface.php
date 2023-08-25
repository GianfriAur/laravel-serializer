<?php

namespace Gianfriaur\Serializer\Service\Engine;

use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;
use Illuminate\Foundation\Application;

interface EngineInterface
{

    public function __construct(Application $app, SerializerInterface $serializer);

    public function getEngineName(): string;

    public function serializeObject(mixed $object, array $group, ?string $metadataProviderClass,  ?array $serializationStack = [], array $options = []);

    public function serializeWithMetadata(mixed $object, array $serialization_metadata, ?array $serializationStack = [], array $options = []);

    public function getEmptySerialization(): mixed;

}
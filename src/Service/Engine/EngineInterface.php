<?php

namespace Gianfriaur\Serializer\Service\Engine;

use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;
use Illuminate\Foundation\Application;

interface EngineInterface
{

    public function __construct( Application $app,SerializerInterface $serializer);

    public function getEngineName(): string;

    public function serializeObject(mixed $object, $serialization_metadata,?array $serializationStack=[]): mixed;

    public function getEmptySerialization(): mixed;

}
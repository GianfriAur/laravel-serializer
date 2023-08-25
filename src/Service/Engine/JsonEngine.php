<?php /** @noinspection PhpClassCanBeReadonlyInspection */

namespace Gianfriaur\Serializer\Service\Engine;

use Gianfriaur\Serializer\Service\Serializer\SerializerInterface;
use Illuminate\Foundation\Application;

class JsonEngine implements EngineInterface
{
    /** @noinspection PhpPropertyOnlyWrittenInspection */
    public function __construct(
        private readonly Application $app,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function getEngineName(): string
    {
        return 'json';
    }

    public function serializeObject(mixed $object, $serialization_metadata, ?array $serializationStack = []): string
    {
        return json_encode(
            $this->serializer->getEngineByNameOrFail('array')->serializeObject($object, $serialization_metadata,$serializationStack)
        );
    }


    public function getEmptySerialization(): string
    {
        return '{}';
    }
}
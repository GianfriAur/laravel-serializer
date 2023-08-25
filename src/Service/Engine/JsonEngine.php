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

    public function serializeObject(mixed $object, array $group, ?string $metadataProviderClass,  ?array $serializationStack = [], array $options = []): string
    {
        return json_encode(
            $this->serializer->getEngineByNameOrFail('array')->serializeObject($object, $group,$metadataProviderClass,$serializationStack,$options)
        );
    }

    public function serializeWithMetadata(mixed $object, array $serialization_metadata, ?array $serializationStack = [], array $options = []):?string
    {
        return json_encode(
            $this->serializer->getEngineByNameOrFail('array')->serializeWithMetadata($object,  $serialization_metadata,  $serializationStack = [],  $options = [])
        );
    }
    public function getEmptySerialization(): string
    {
        return '{}';
    }
}
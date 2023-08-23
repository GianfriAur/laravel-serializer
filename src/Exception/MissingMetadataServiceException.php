<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class MissingMetadataServiceException extends SerializerException
{

    public function __construct(string $metadataServiceClass, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("MetadataService named $metadataServiceClass is missing", $code, $previous);
    }

}
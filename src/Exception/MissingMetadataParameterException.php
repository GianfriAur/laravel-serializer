<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class MissingMetadataParameterException extends SerializerException
{
    public function __construct(string $property_name = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('In the metadata the property \''.$property_name.'\' is missing', $code, $previous);
    }
}
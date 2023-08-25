<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class SerializationIsNotAllowedForTypeException extends SerializerException
{

    public function __construct(string $property, string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Serializing the object the property \''.$property.'\' is of type \''.$type.'\' where it is not possible to serialize its content', $code, $previous);
    }

}
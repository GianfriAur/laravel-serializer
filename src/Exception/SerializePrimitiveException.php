<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class SerializePrimitiveException extends SerializerException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('It is not possible to serialize primitive types [\'boolean\', \'integer\', \'double\', \'string\'] as the main objects of the serialization. You can set the \'serialize_primitive options\' to true, but it\'s not recommended', $code, $previous);
    }
}
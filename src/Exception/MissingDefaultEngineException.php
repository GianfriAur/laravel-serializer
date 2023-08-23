<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class MissingDefaultEngineException extends SerializerException
{

    public function __construct( int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("In the serialization service the default serializer is missing", $code, $previous);
    }

}
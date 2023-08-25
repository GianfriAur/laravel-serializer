<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class SerializationMissingGetStrategyException extends SerializerException
{

    public function __construct($property_name ,int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('In the metadata it is not indicated how to access app properties: \''.$property_name.'\'', $code, $previous);
    }
}
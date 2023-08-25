<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class ObjectMissingMethodException extends SerializerException
{

    public function __construct(mixed $object, string $property_name, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The object '.get_class($object).' haven\'t the following method: '.$property_name, $code, $previous);
    }

}
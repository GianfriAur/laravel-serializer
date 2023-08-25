<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class RecursiveSerializationException extends SerializerException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('A recursive serialization has been found, you should review the serialization groups to avoid this problem, but if it is essential you can still activate the \'prevent_recursive_serialization\' setting, this will prevent a child from serializing the parent, this setting is not recommended', $code, $previous);
    }
}
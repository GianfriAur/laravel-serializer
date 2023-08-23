<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class EngineAlreadyRegisteredException extends SerializerException
{

    public function __construct(string $engine_mame, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Engine named $engine_mame already registered", $code, $previous);
    }

}
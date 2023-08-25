<?php

namespace Gianfriaur\Serializer\Exception;

use Throwable;

class MissingGetTypeStrategyException extends SerializerException
{

    public function __construct(string $strategy = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The strategy \''.$strategy.'\' isn\'t recognized', $code, $previous);
    }

}
<?php

namespace Gianfriaur\Serializer\Exception;

use Gianfriaur\Serializer\SerializerServiceProvider;
use Throwable;

class SerializerMissingConfigException extends SerializerException
{
    public function __construct(string $config_name = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            "Serializer cannot load services, $config_name config in file 'config/" . SerializerServiceProvider::CONFIG_FILE_NANE . '\'',
            $code, $previous);
    }
}
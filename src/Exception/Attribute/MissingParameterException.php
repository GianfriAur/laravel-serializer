<?php

namespace Gianfriaur\Serializer\Exception\Attribute;

use Throwable;

class MissingParameterException extends AttributeSerializerException
{

    public function __construct( $attributeClass, $parameterName, int $code = 0, ?Throwable $previous = null)
    {
        $classname = last(explode('\\',$attributeClass));

        parent::__construct(
            'In the attribute \''.$classname.'\' the parameter \''.$parameterName.'\' is missing, to solve it add: #['.$classname.'( ... , '.$parameterName.': MY_VALUE )]', $code, $previous);
    }
}
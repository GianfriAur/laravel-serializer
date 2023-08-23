<?php

namespace Gianfriaur\Serializer\Service\AnnotationParserService;

use Illuminate\Foundation\Application;

interface AnnotationParserServiceInterface
{
    public function __construct(Application $app, array $options);

    function getObjectSerializationInformation($className);
}
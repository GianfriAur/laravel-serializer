<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Service
    |--------------------------------------------------------------------------
    |
    | is the service that takes care of saving the results of other services
    |   to increase performance
    |
    */
    'cache_service' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Cache Services
    |--------------------------------------------------------------------------
    |
    | list of all cache service available for the library
    |
    */
    'cache_services' => [
        'default' => [
            'class' => \Gianfriaur\FastCache\Service\CacheService\DefaultCacheService::class,
            'options' => [
                'cache_file' => 'cache/serializations.php',
                'file_env_override' => 'SERIALIZER_CACHE_FILE',
                'store' => \Gianfriaur\FastCache\Cache\Stores\FileArrayStore::class,
                'driver_name' => 'serializations'
            ]
        ]
    ],
];
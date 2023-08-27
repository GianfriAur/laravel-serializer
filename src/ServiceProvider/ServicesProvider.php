<?php

namespace Gianfriaur\Serializer\ServiceProvider;

use Gianfriaur\FastCache\Service\CacheServiceRegister\DefaultCacheServiceRegister;
use Gianfriaur\Serializer\Exception\SerializerMissingConfigException;
use Gianfriaur\Serializer\Service\CacheService\SerializerCacheServiceInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ServicesProvider extends ServiceProvider implements DeferrableProvider
{
    const CONFIG_NAMESPACE = "serializer";
    const CONFIG_FILE_NANE = "serializer.php";


    public function register(): void
    {
        [$cache_class, $cache_options] = $this->getGenericServiceDefinition('cache_service', 'cache_services', false);
        DefaultCacheServiceRegister::registerCacheService(
            $this->app,
            SerializerCacheServiceInterface::class,
            $cache_class,
            $cache_options,
            'serializer.cache_service',
        );
    }

    /**
     * @throws SerializerMissingConfigException
     */
    private function getGenericServiceDefinition(string $strategy_name, string $strategy_collection_name, bool $nullable = false): array
    {
        $strategy = $this->getConfig($strategy_name, $nullable);
        if ($strategy === null) return [null, []];
        $strategy_collection = $this->getConfig($strategy_collection_name);
        return [$strategy_collection[$strategy]['class'], $strategy_collection[$strategy]['options']];
    }

    /**
     * @throws SerializerMissingConfigException
     */
    private function getConfig($name, bool $nullable = false): mixed
    {
        if (!$config = config(self::CONFIG_NAMESPACE . '.' . $name)) {
            if (!$nullable) throw new SerializerMissingConfigException($name);
        }
        return $config;
    }

    public function provides()
    {
        // TODO: Implement provides() method.
    }
}
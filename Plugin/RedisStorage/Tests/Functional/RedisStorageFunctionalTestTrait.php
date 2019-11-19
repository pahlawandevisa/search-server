<?php


namespace Apisearch\Plugin\RedisStorage\Tests\Functional;

use Apisearch\Plugin\RedisStorage\RedisStoragePluginBundle;

/**
 * Trait RedisStorageFunctionalTestTrait
 */
trait RedisStorageFunctionalTestTrait
{
    /**
     * Decorate bundles.
     *
     * @param array $bundles
     *
     * @return array
     */
    protected static function decorateBundles(array $bundles): array
    {
        $bundles[] = RedisStoragePluginBundle::class;

        return $bundles;
    }

    /**
     * Save events.
     *
     * @return bool
     */
    protected static function saveEvents(): bool
    {
        return false;
    }
}
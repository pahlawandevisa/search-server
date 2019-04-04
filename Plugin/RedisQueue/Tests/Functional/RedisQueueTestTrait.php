<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\RedisQueue\Tests\Functional;

use Apisearch\Plugin\RedisQueue\RedisQueuePluginBundle;

/**
 * Class RedisQueueTestTrait.
 */
trait RedisQueueTestTrait
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
        $bundles = parent::decorateBundles($bundles);
        $bundles[] = RedisQueuePluginBundle::class;

        return $bundles;
    }

    /**
     * Force all connections to be droped.
     */
    protected function dropConnections()
    {
        $redisClient = $this->get('apisearch_plugin.redis_queue.redis_wrapper')->getClient();
        $openedClientsList = $redisClient->client('list');
        foreach ($openedClientsList as $openedClient) {
            if ('blpop' === strtolower($openedClient['cmd'])) {
                $redisClient->client('kill', $openedClient['addr']);
            }
        }
    }
}

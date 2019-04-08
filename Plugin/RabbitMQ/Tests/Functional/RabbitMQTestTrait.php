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

namespace Apisearch\Plugin\RabbitMQ\Tests\Functional;

use Apisearch\Plugin\RabbitMQ\RabbitMQPluginBundle;

/**
 * Class RabbitMQTestTrait.
 */
trait RabbitMQTestTrait
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
        $bundles[] = RabbitMQPluginBundle::class;

        return $bundles;
    }

    /**
     * Force all connections to be droped.
     */
    protected function dropConnections()
    {
        $host = sprintf('http://%s:%s@%s:%s/api/',
            $_ENV['RABBITMQ_QUEUE_USER'],
            $_ENV['RABBITMQ_QUEUE_PASSWORD'],
            $_ENV['RABBITMQ_QUEUE_HOST'],
            '15672'
        );

        $connections = file_get_contents($host.'connections');
        $connections = json_decode($connections, true);

        foreach ($connections as $connection) {
            file_get_contents(
                $host.'connections/'.rawurlencode($connection['name']),
                false,
                stream_context_create([
                    'http' => [
                        'method' => 'DELETE',
                    ],
                ])
            );
        }
    }
}

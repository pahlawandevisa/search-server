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

namespace Apisearch\Plugin\RedisQueue\Domain;

use Apisearch\Plugin\Redis\Domain\RedisWrapper;
use Apisearch\Reconnect\PHPRedisReconnect;
use Apisearch\Server\Domain\Consumer\ConsumerManager;
use Redis;

/**
 * Class RedisQueueConsumerManager.
 */
class RedisQueueConsumerManager extends ConsumerManager
{
    /**
     * @var RedisWrapper
     *
     * Redis wrapper
     */
    private $redisWrapper;

    /**
     * RedisQueueClient constructor.
     *
     * @param array        $queues
     * @param RedisWrapper $redisWrapper
     */
    public function __construct(
        array $queues,
        RedisWrapper $redisWrapper
    ) {
        parent::__construct($queues);
        $this->redisWrapper = $redisWrapper;
    }

    /**
     * Get Client.
     *
     * @return Redis|RedisCluster
     */
    private function getClient()
    {
        return $this
            ->redisWrapper
            ->getClient();
    }

    /**
     * Get config for PHPReconnect.
     *
     * @return array
     */
    private function getConfigForPHPReconnect(): array
    {
        $config = $this
            ->redisWrapper
            ->getRedisConfig();

        return [
            'host' => $config->getHost(),
            'port' => $config->getPort(),
            'database' => $config->getDatabase(),
        ];
    }

    /**
    /**
     * Declare busy channel.
     *
     * @param string $type
     * @param mixed  $data
     */
    public function enqueue(
        string $type,
        $data
    ) {
        PHPRedisReconnect::tryOrReconnect(
            function ($client) use ($type, $data) {
                $client->rPush(
                    $this->queues['queues'][$type],
                    json_encode($data)
                );
            },
            $this->getClient(),
            $this->getConfigForPHPReconnect()
        );
    }

    /**
     * Get queue size.
     *
     * @param string $type
     *
     * @return int|null
     */
    public function getQueueSize(string $type): ? int
    {
        $queueName = $this->queues['queues'][$type] ?? null;

        if (is_null($queueName)) {
            return false;
        }

        return PHPRedisReconnect::tryOrReconnect(
            function ($client) use ($queueName) {
                return $client->lLen($queueName);
            },
            $this->getClient(),
            $this->getConfigForPHPReconnect()
        );
    }

    /**
     * Reject message. Enqueue it in the original position.
     *
     * @param string $queue
     * @param array  $payload
     */
    public function reject(
        string $queue,
        array $payload
    ) {
        PHPRedisReconnect::tryOrReconnect(
            function ($client) use ($queue, $payload) {
                $client->lPush(
                    $queue,
                    json_encode($payload)
                );
            },
            $this->getClient(),
            $this->getConfigForPHPReconnect()
        );
    }

    /**
     * Consume message.
     *
     * @param string $queueName
     *
     * @return array
     */
    public function consume(string $queueName): array
    {
        list($queueName, $payload) = PHPRedisReconnect::tryOrReconnect(
            function ($client) use ($queueName) {
                return $client->blPop(
                    [
                        $this->queues['busy_queues'][$queueName],
                        $this->queues['queues'][$queueName],
                    ], 0
                );
            },
            $this->getClient(),
            $this->getConfigForPHPReconnect()
        );

        return [$queueName, json_decode($payload, true)];
    }

    /**
     * Send to queues a boolean value, given queues.
     *
     * @param string[] $queues
     * @param bool     $value
     */
    protected function sendBooleanToQueues(
        array $queues,
        bool $value
    ) {
        foreach ($queues as $queue) {
            PHPRedisReconnect::tryOrReconnect(
                function ($client) use ($queue, $value) {
                    return $client->rPush(
                        $queue,
                        json_encode($value)
                    );
                },
                $this->getClient(),
                $this->getConfigForPHPReconnect()
            );
        }
    }
}

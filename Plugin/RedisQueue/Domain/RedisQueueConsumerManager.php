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
use Apisearch\Server\Domain\Consumer\ConsumerManager;

/**
 * Class RedisQueueConsumerManager.
 */
class RedisQueueConsumerManager extends ConsumerManager
{
    use RedisQueueTrierTrait;

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
        $this->tryActionNTimes(
            $this->redisWrapper,
            function ($client) use ($type, $data) {
                $client->rPush(
                    $this->queues['queues'][$type],
                    json_encode($data)
                );
            },
            3
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

        return (int) $this->tryActionNTimes(
            $this->redisWrapper,
            function ($client) use ($queueName) {
                return $client->lLen($queueName);
            },
            3
        );
    }

    /**
     * Produce message.
     *
     * @param string $queue
     * @param array  $payload
     */
    public function reject(
        string $queue,
        array $payload
    ) {
        $this->tryActionNTimes(
            $this->redisWrapper,
            function ($client) use ($queue, $payload) {
                $client->lPush(
                    $queue,
                    json_encode($payload)
                );
            },
            3
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
        list($queueName, $payload) = $this->tryActionNTimes(
            $this->redisWrapper,
            function ($client) use ($queueName) {
                return $client->blPop(
                    [
                        $this->queues['busy_queues'][$queueName],
                        $this->queues['queues'][$queueName],
                    ], 0
                );
            },
            3
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
            $this->tryActionNTimes(
                $this->redisWrapper,
                function ($client) use ($queue, $value) {
                    return $client->rPush(
                        $queue,
                        json_encode($value)
                    );
                },
                3
            );
        }
    }
}

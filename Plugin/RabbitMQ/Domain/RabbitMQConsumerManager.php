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

namespace Apisearch\Plugin\RabbitMQ\Domain;

use Apisearch\Server\Domain\Consumer\ConsumerManager;
use Bunny\Channel;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\Promise;

/**
 * Class RabbitMQConsumerManager.
 */
class RabbitMQConsumerManager extends ConsumerManager
{
    /**
     * @var RabbitMQClient
     *
     * Client
     */
    private $client;

    /**
     * RabbitMQConsumerManager constructor.
     *
     * @param array           $queues
     * @param RabbitMQClient $client
     */
    public function __construct(
        array $queues,
        RabbitMQClient $client
    ) {
        parent::__construct($queues);
        $this->client = $client;
    }

    /**
     * Get client.
     *
     * @return PromiseInterface
     */
    private function getChannel(): PromiseInterface
    {
        return $this
            ->client
            ->getChannel();
    }

    /**
     * Declare consumer and return if was ok.
     *
     * @param string $type
     *
     * @return PromiseInterface<bool>
     */
    public function declareConsumer(string $type): PromiseInterface
    {
        $queueName = $this->queues['queues'][$type] ?? null;

        if (is_null($queueName)) {
            return new FulfilledPromise(false);
        }

        return $this
            ->getChannel()
            ->then(function(Channel $channel) use ($queueName) {
                return $channel->queueDeclare($queueName);
            });
    }

    /**
     * Declare busy channel and return the queue name if was ok.
     *
     * @param string $type
     *
     * @return PromiseInterface<string|null>
     */
    public function declareBusyChannel(string $type): PromiseInterface
    {
        $busyQueueName = $this->queues['busy_queues'][$type] ?? null;
        if (is_null($busyQueueName)) {
            return new FulfilledPromise(null);
        }

        return $this
            ->getChannel()
            ->then(function(Channel $channel) use ($busyQueueName) {
                return $channel
                    ->exchangeDeclare($busyQueueName, 'fanout')
                    ->then(function() use ($channel) {

                        return $channel->queueDeclare('', false, false, true);
                    })
                    ->then(function(MethodQueueDeclareOkFrame $result) use ($channel, $busyQueueName) {
                        $createdBusyQueueName = $result->queue;

                        return $channel
                            ->queueBind($createdBusyQueueName, $busyQueueName)
                            ->then(function() use ($createdBusyQueueName) {
                                return $createdBusyQueueName;
                            });
                    });
            });
    }

    /**
     * Declare busy channel.
     *
     * @param string $type
     * @param mixed  $data
     *
     * @return PromiseInterface
     */
    public function enqueue(
        string $type,
        $data
    ): PromiseInterface {
        if (is_null($this->declareConsumer($type))) {
            return new FulfilledPromise(null);
        }

        return $this
            ->getChannel()
            ->then(function(Channel $channel) use ($data, $type) {
                return $channel->publish(json_encode($data), [
                    'delivery_mode' => 2,
                ], '', $this->queues['queues'][$type]);
            });
    }

    /**
     * Get queue size.
     *
     * @param string $type
     *
     * @return PromiseInterface<int|null>
     */
    public function getQueueSize(string $type): PromiseInterface
    {
        $queueName = $this->queues['queues'][$type] ?? null;

        if (is_null($queueName)) {
            return new FulfilledPromise(null);
        }

        return $this
            ->getChannel()
            ->then(function(Channel $channel) use ($queueName) {
                return $channel->queueDeclare($queueName, true);
            })
            ->then(function(MethodQueueDeclareOkFrame $response) {

                return $response->messageCount;
            });
    }

    /**
     * Send to queues a boolean value, given queues.
     *
     * @param string[] $queues
     * @param bool     $value
     *
     * @return PromiseInterface
     */
    protected function sendBooleanToQueues(
        array $queues,
        bool $value
    ): PromiseInterface {

        $channelPromise = $this->getChannel();
        $promises = [];
        foreach ($queues as $queue) {
            $promises[] = $channelPromise->then(function(Channel $channel) use ($value, $queue) {
                return $channel->publish($value, [], $queue);
            });
        }

        return Promise\all($promises);
    }
}

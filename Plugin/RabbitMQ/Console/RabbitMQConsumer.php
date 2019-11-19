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

namespace Apisearch\Plugin\RabbitMQ\Console;

use Apisearch\Command\ApisearchCommand;
use Apisearch\Plugin\RabbitMQ\Domain\RabbitMQClient;
use Apisearch\Server\Domain\Consumer\ConsumerManager;
use Bunny\Channel;
use Bunny\Message;
use Mmoreram\React;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RabbitMQConsumer.
 */
abstract class RabbitMQConsumer extends ApisearchCommand
{
    /**
     * @var RabbitMQClient
     *
     * Client
     */
    protected $client;

    /**
     * @var ConsumerManager
     *
     * Consumer manager
     */
    protected $consumerManager;

    /**
     * @var LoopInterface
     *
     * Loop
     */
    protected $loop;

    /**
     * @var int
     *
     * Seconds to wait on busy
     */
    private $secondsToWaitOnBusy;

    /**
     * @var bool
     *
     * Busy
     */
    protected $busy = false;

    /**
     * ConsumerCommand constructor.
     *
     * @param RabbitMQClient  $client
     * @param ConsumerManager $consumerManager
     * @param LoopInterface   $loop
     * @param int             $secondsToWaitOnBusy
     */
    public function __construct(
        RabbitMQClient $client,
        ConsumerManager $consumerManager,
        LoopInterface $loop,
        int $secondsToWaitOnBusy
    ) {
        parent::__construct();

        $this->client = $client;
        $this->consumerManager = $consumerManager;
        $this->loop = $loop;
        $this->secondsToWaitOnBusy = $secondsToWaitOnBusy;
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
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int|null null or 0 if everything went fine, or an error code
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommand($output);
        $queueType = $this->getQueueType();

        $this
            ->consumerManager
            ->declareConsumer($queueType)
            ->then(function () use ($output) {
                self::printInfoMessage($output, 'RabbitMQ', 'Connecting...');
                $this
                    ->getChannel()
                    ->then(function (Channel $channel) use ($output) {
                        return $this->bindCallbacksToChannel($channel, $output);
                    });
            });

        $this
            ->loop
            ->run();

        return 0;
    }

    /**
     * Bind all callbacks to channel.
     *
     * @param Channel         $channel
     * @param OutputInterface $output
     *
     * @return PromiseInterface
     */
    private function bindCallbacksToChannel(
        Channel $channel,
        OutputInterface $output
    ): PromiseInterface {
        $consumerManager = $this->consumerManager;
        $queueType = $this->getQueueType();
        $consumerQueueName = $consumerManager->getQueueName($queueType, false);

        return $channel
            ->qos(0, 1, false)
            ->then(function () use ($consumerQueueName, $output, $channel) {
                return $channel
                    ->consume(function (Message $message, Channel $channel) use ($output) {
                        if ($this->busy) {
                            self::printInfoMessage($output, 'RabbitMQ', 'Busy channel. Rejecting and waiting '.$this->secondsToWaitOnBusy.' seconds');

                            return $channel
                                ->reject($message)
                                ->then(function () {
                                    return React\sleep(
                                        $this->secondsToWaitOnBusy,
                                        $this->loop
                                    );
                                });
                        }

                        return $this->consumeMessage(
                            $message,
                            $channel,
                            $output
                        );
                    }, $consumerQueueName);
            })
            ->then(function () use ($queueType, $output, $channel) {
                return $this
                    ->consumerManager
                    ->declareBusyChannel($queueType)
                    ->then(function ($busyGeneratedQueue) use ($output, $channel) {
                        return $channel->consume(function (Message $message) use ($output) {
                            $this->busy = boolval($message->content);
                            self::printInfoMessage($output, 'RabbitMQ', ($this->busy ? 'Paused' : 'Resumed').' consumer');
                        }, $busyGeneratedQueue, '', false, true);
                    });
            });
    }

    /**
     * Get queue type.
     *
     * @return string
     */
    abstract protected function getQueueType(): string;

    /**
     * Consume message.
     *
     * @param Message         $message
     * @param Channel         $channel
     * @param OutputInterface $output
     *
     * @return PromiseInterface
     */
    abstract protected function consumeMessage(
        Message $message,
        Channel $channel,
        OutputInterface $output
    ): PromiseInterface;
}

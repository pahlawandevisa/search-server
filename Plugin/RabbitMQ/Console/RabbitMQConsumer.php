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
use Apisearch\Plugin\RabbitMQ\Domain\RabbitMQChannel;
use Apisearch\Reconnect\AMQPReconnect;
use Apisearch\Server\Domain\Consumer\ConsumerManager;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RabbitMQConsumer.
 */
abstract class RabbitMQConsumer extends ApisearchCommand
{
    /**
     * @var RabbitMQChannel
     *
     * Channel
     */
    protected $channel;

    /**
     * @var ConsumerManager
     *
     * Consumer manager
     */
    protected $consumerManager;

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
     * @param RabbitMQChannel $channel
     * @param ConsumerManager $consumerManager
     * @param int             $secondsToWaitOnBusy
     */
    public function __construct(
        RabbitMQChannel        $channel,
        ConsumerManager $consumerManager,
        int $secondsToWaitOnBusy
    ) {
        parent::__construct();

        $this->channel = $channel;
        $this->consumerManager = $consumerManager;
        $this->secondsToWaitOnBusy = $secondsToWaitOnBusy;
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
            ->declareConsumer($queueType);

        AMQPReconnect::tryOrReconnect(
            function (AbstractConnection $connection) use ($output) {
                self::printInfoMessage($output, 'RabbitMQ', 'Connecting...');
                $channel = $connection->channel();
                $this->bindCallbacksToChannel($channel, $output);
                while (count($channel->callbacks)) {
                    $channel->wait();
                }
            },
            $this
                ->channel
                ->getConnection()
        );

        return 0;
    }

    /**
     * Bind all callbacks to channel.
     *
     * @param AMQPChannel     $channel
     * @param OutputInterface $output
     */
    private function bindCallbacksToChannel(
        AMQPChannel $channel,
        OutputInterface $output
    ) {
        $consumerManager = $this->consumerManager;
        $queueType = $this->getQueueType();
        $consumerQueueName = $consumerManager->getQueueName($queueType, false);
        $channel->basic_qos(0, 1, false);
        $channel->basic_consume($consumerQueueName, '', false, false, false, false, function (AMQPMessage $message) use ($output, $channel) {
            if ($this->busy) {
                self::printInfoMessage($output, 'RabbitMQ', 'Busy channel. Rejecting and waiting '.$this->secondsToWaitOnBusy.' seconds');
                $channel->basic_reject($message->delivery_info['delivery_tag'], true);
                sleep($this->secondsToWaitOnBusy);

                return;
            }

            $this->consumeMessage(
                $message,
                $channel,
                $output
            );
        });

        $busyGeneratedQueue = $this
            ->consumerManager
            ->declareBusyChannel($queueType);

        $channel->basic_consume($busyGeneratedQueue, '', false, true, false, false, function (AMQPMessage $message) use ($output) {
            $this->busy = boolval($message->body);
            self::printInfoMessage($output, 'RabbitMQ', ($this->busy ? 'Paused' : 'Resumed').' consumer');
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
     * @param AMQPMessage     $message
     * @param AMQPChannel     $channel
     * @param OutputInterface $output
     */
    abstract protected function consumeMessage(
        AMQPMessage $message,
        AMQPChannel $channel,
        OutputInterface $output
    );
}
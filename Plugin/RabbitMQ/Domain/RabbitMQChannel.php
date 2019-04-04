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

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class RabbitMQChannel.
 */
class RabbitMQChannel
{
    /**
     * @var string
     *
     * Host
     */
    private $host;

    /**
     * @var int
     *
     * Port
     */
    private $port;

    /**
     * @var string
     *
     * User
     */
    private $user;

    /**
     * @var string
     *
     * Password
     */
    private $password;

    /**
     * @var string
     *
     * Vhost
     */
    private $vhost;

    /**
     * @var AMQPChannel
     *
     * Channel
     */
    private $channel;

    /**
     * RabbitMQChannel constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        string $vhost
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    /**
     * Get channel.
     *
     * @param bool $forceNew
     *
     * @return AMQPChannel
     */
    public function getChannel($forceNew = false): AMQPChannel
    {
        if (
            !$forceNew &&
            $this->channel instanceof AMQPChannel &&
            $this->channel->is_open()
        ) {
            return $this->channel;
        }

        $this->channel = $this->getFreshNewConnection();

        return $this->channel;
    }

    /**
     * Get a fresh new channel connection.
     */
    private function getFreshNewConnection()
    {
        return (new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vhost
        ))->channel();
    }
}

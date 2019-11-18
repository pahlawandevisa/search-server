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

use Bunny\Async\Client;
use Bunny\Channel;
use React\EventLoop\LoopInterface;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use Clue\React\Block;

/**
 * Class RabbitMQClient.
 */
class RabbitMQClient
{
    /**
     * @var LoopInterface
     *
     * Loop
     */
    private $loop;

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
     * @var Client
     *
     * Channel
     */
    private $client;

    /**
     * @var Channel
     *
     * Channel
     */
    private $channel;

    /**
     * RabbitMQChannel constructor.
     *
     * @param LoopInterface $loop
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     */
    public function __construct(
        LoopInterface $loop,
        string $host,
        int $port,
        string $user,
        string $password,
        string $vhost
    ) {
        $this->loop = $loop;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    /**
     * Build the client and connect it synchronously
     */
    private function buildAndSyncConnectClient()
    {
        $client = new Client($this->loop, [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'password' => $this->password,
            'vhost' => $this->vhost
        ]);

        $this->client = Block\await($client->connect(), $this->loop);
        $this->channel = Block\await($client->channel(), $this->loop);
    }

    /**
     * Get channel
     *
     * @return PromiseInterface
     */
    public function getChannel() : PromiseInterface
    {
        if (!$this->channel instanceof Channel) {
            $this->buildAndSyncConnectClient();
        }

        return new FulfilledPromise($this->channel);
    }
}

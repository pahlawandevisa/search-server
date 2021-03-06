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
use Clue\React\Block;
use React\EventLoop\LoopInterface;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;

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
     * @var Channel
     *
     * Channel
     */
    private $channel;

    /**
     * RabbitMQChannel constructor.
     *
     * @param LoopInterface $loop
     * @param string        $host
     * @param int           $port
     * @param string        $user
     * @param string        $password
     * @param string        $vhost
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
     * Build the client and connect it synchronously.
     */
    private function buildAndSyncConnectClient()
    {
        $client = new Client($this->loop, [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'password' => $this->password,
            'vhost' => $this->vhost,
        ]);

        return $client
            ->connect()
            ->then(function(Client $client) {
                return $client
                    ->channel()
                    ->then(function(Channel $channel) {
                        $this->channel = $channel;

                        return $channel;
                    });
            });
    }

    /**
     * Get channel.
     *
     * @return PromiseInterface
     */
    public function getChannel(): PromiseInterface
    {
        if (!$this->channel instanceof Channel) {
            return $this->buildAndSyncConnectClient();
        }

        return new FulfilledPromise($this->channel);
    }
}

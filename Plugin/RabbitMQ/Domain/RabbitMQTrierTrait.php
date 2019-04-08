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

use Apisearch\Server\Exception\ExternalResourceException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;

/**
 * Class RabbitMQTrierTrait.
 */
trait RabbitMQTrierTrait
{
    /**
     * Try action n times.
     *
     * @param RabbitMQChannel $channel
     * @param \Closure        $callback
     * @param int             $times
     * @param int             $secondsBetweenTries
     *
     * @return mixed
     *
     * @throws ExternalResourceException
     */
    private function tryActionNTimes(
        RabbitMQChannel $channel,
        \Closure $callback,
        int $times,
        int $secondsBetweenTries = 0
    ) {
        $iterations = $times;
        $firstTime = true;
        while (0 !== $iterations) {
            try {
                return $callback($channel->getChannel(!$firstTime));
            } catch (AMQPConnectionClosedException $exception) {
                // Silent pass
            } catch (AMQPIOException $exception) {
                // Silent pass
            } catch (AMQPProtocolConnectionException $exception) {
                // Silent pass
            }

            $iterations = $iterations > 0
                ? $iterations - 1
                : $iterations;

            if (
                0 === $iterations
            ) {
                throw ExternalResourceException::createExternalConnectionException('Rabbitmq');
            }

            $firstTime = false;
            sleep($secondsBetweenTries);
        }
    }
}

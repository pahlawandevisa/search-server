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
use Apisearch\Server\Exception\ExternalResourceException;

/**
 * Class RedisQueueTrierTrait.
 */
trait RedisQueueTrierTrait
{
    /**
     * Try action n times.
     *
     * @param RedisWrapper $wrapper
     * @param \Closure     $callback
     * @param int          $times
     * @param int          $secondsBetweenTries
     *
     * @return mixed
     *
     * @throws ExternalResourceException
     */
    private function tryActionNTimes(
        RedisWrapper $wrapper,
        \Closure $callback,
        int $times,
        int $secondsBetweenTries = 0
    ) {
        $iterations = $times;
        $firstTime = true;
        while (0 !== $iterations) {
            try {
                return $callback($wrapper->getClient(!$firstTime));
            } catch (\RedisException $exception) {
                // Silent pass
            }

            $iterations = $iterations > 0
                ? $iterations - 1
                : $iterations;

            if (
                0 === $iterations
            ) {
                throw ExternalResourceException::createExternalConnectionException('Redis');
            }

            $firstTime = false;
            sleep($secondsBetweenTries);
        }
    }
}

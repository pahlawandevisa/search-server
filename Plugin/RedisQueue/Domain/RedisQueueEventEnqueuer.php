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

use Apisearch\Server\Domain\Consumer\ConsumerManager;
use Apisearch\Server\Domain\EventEnqueuer\EventEnqueuer;
use React\Promise\PromiseInterface;

/**
 * Class RedisQueueEventEnqueuer.
 */
class RedisQueueEventEnqueuer implements EventEnqueuer
{
    /**
     * @var RedisQueueConsumerManager
     *
     * Consumer Manager
     */
    protected $consumerManager;

    /**
     * RSQueueEventEnqueuer constructor.
     *
     * @param RedisQueueConsumerManager $consumerManager
     */
    public function __construct(RedisQueueConsumerManager $consumerManager)
    {
        $this->consumerManager = $consumerManager;
    }

    /**
     * Enqueue a domain event.
     *
     * @param array $event
     *
     * @return PromiseInterface
     */
    public function enqueueEvent(array $event): PromiseInterface
    {
        return $this
            ->consumerManager
            ->enqueue(
                ConsumerManager::DOMAIN_EVENT_CONSUMER_TYPE,
                $event
            );
    }
}

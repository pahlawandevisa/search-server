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

namespace Apisearch\Server\Domain\QueryHandler;

use Apisearch\Model\Item;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\QueryWasMade;
use Apisearch\Server\Domain\Query\Query;
use Apisearch\Server\Domain\WithRepositoryAndEventPublisher;
use Ramsey\Uuid\Uuid;

/**
 * Class QueryHandler.
 */
class QueryHandler extends WithRepositoryAndEventPublisher
{
    /**
     * Reset the query.
     *
     * @param Query $query
     *
     * @return Result
     */
    public function handle(Query $query)
    {
        $repositoryReference = $query->getRepositoryReference();
        $searchQuery = $query->getQuery();
        $from = microtime(true);

        $this->assignUUIDIfNeeded($query);

        $this
            ->repository
            ->setRepositoryReference($query->getRepositoryReference());

        $result = $this
            ->repository
            ->query($searchQuery);

        $this
            ->eventPublisher
            ->publish(new DomainEventWithRepositoryReference(
                $repositoryReference,
                new QueryWasMade(
                    $searchQuery->getQueryText(),
                    $searchQuery->getSize(),
                    array_map(function (Item $item) {
                        return $item->getUUID();
                    }, $result->getItems()),
                    $searchQuery->getUser(),
                    json_encode($query->getQuery()->toArray())
                ),
                (int) ((microtime(true) - $from) * 1000)
            ));

        return $result;
    }

    /**
     * Add UUID into query if needed.
     *
     * @param Query $query
     */
    private function assignUUIDIfNeeded(Query $query)
    {
        $queryModel = $query->getQuery();
        if (empty($queryModel->getUUID())) {
            $queryModel->identifyWith(Uuid::uuid4()->toString());
        }
    }
}

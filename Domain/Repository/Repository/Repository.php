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

namespace Apisearch\Server\Domain\Repository\Repository;

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Changes;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Result;

/**
 * Class Repository.
 */
class Repository
{
    /**
     * @var ItemsRepository
     *
     * Items repository
     */
    private $itemsRepository;

    /**
     * @var QueryRepository
     *
     * Query Repository
     */
    private $queryRepository;

    /**
     * Repository constructor.
     *
     * @param ItemsRepository $itemsRepository
     * @param QueryRepository $queryRepository
     */
    public function __construct(
        ItemsRepository $itemsRepository,
        QueryRepository $queryRepository
    ) {
        $this->itemsRepository = $itemsRepository;
        $this->queryRepository = $queryRepository;
    }

    /**
     * Add items.
     *
     * @param RepositoryReference $repositoryReference
     * @param Item[]              $items
     */
    public function addItems(
        RepositoryReference $repositoryReference,
        array $items
    ) {
        $this
            ->itemsRepository
            ->addItems(
                $repositoryReference,
                $items
            );
    }

    /**
     * Delete items.
     *
     * @param RepositoryReference $repositoryReference
     * @param ItemUUID[]          $itemsUUID
     */
    public function deleteItems(
        RepositoryReference $repositoryReference,
        array $itemsUUID
    ) {
        $this
            ->itemsRepository
            ->deleteItems(
                $repositoryReference,
                $itemsUUID
            );
    }

    /**
     * Search across the index types.
     *
     * @param RepositoryReference $repositoryReference
     * @param Query               $query
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        RepositoryReference $repositoryReference,
        Query $query
    ): Result {
        return $this
            ->queryRepository
            ->query(
                $repositoryReference,
                $query
            );
    }

    /**
     * Update items.
     *
     * @param RepositoryReference $repositoryReference
     * @param Query               $query
     * @param Changes             $changes
     */
    public function updateItems(
        RepositoryReference $repositoryReference,
        Query $query,
        Changes $changes
    ) {
        $this
            ->itemsRepository
            ->updateItems(
                $repositoryReference,
                $query,
                $changes
            );
    }
}

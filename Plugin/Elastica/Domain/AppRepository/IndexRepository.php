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

namespace Apisearch\Plugin\Elastica\Domain\AppRepository;

use Apisearch\Config\Config;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Plugin\Elastica\Domain\WithElasticaWrapper;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Repository\AppRepository\IndexRepository as IndexRepositoryInterface;
use Elastica\Exception\ResponseException;

/**
 * Class IndexRepository.
 */
class IndexRepository extends WithElasticaWrapper implements IndexRepositoryInterface
{
    /**
     * Get indices.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Index[]
     */
    public function getIndices(RepositoryReference $repositoryReference): array
    {
        try {
            return $this
                ->elasticaWrapper
                ->getIndices($repositoryReference);
        } catch (ResponseException $exception) {
            // Silent pass
        }

        return [];
    }

    /**
     * Create an index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     * @param Config              $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID,
        Config $config
    ) {
        $newRepositoryReference = $repositoryReference->changeIndex($indexUUID);

        $this
            ->elasticaWrapper
            ->createIndex(
                $newRepositoryReference,
                $config
            );

        $this
            ->elasticaWrapper
            ->createIndexMapping(
                $newRepositoryReference,
                $config
            );

        $this->refresh($newRepositoryReference);
    }

    /**
     * Delete the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     */
    public function deleteIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID
    ) {
        $this
            ->elasticaWrapper
            ->deleteIndex($repositoryReference->changeIndex($indexUUID));
    }

    /**
     * Reset the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     */
    public function resetIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID
    ) {
        $newRepositoryReference = $repositoryReference->changeIndex($indexUUID);

        $this
            ->elasticaWrapper
            ->resetIndex($newRepositoryReference);

        $this->refresh($newRepositoryReference);
    }

    /**
     * Configure the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     * @param Config              $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID,
        Config $config
    ) {
        $newRepositoryReference = $repositoryReference->changeIndex($indexUUID);

        $this
            ->elasticaWrapper
            ->configureIndex(
                $newRepositoryReference,
                $config
            );

        $this->refresh($newRepositoryReference);
    }
}

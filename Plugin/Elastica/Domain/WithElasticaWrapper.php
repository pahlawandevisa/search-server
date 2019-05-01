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

namespace Apisearch\Plugin\Elastica\Domain;

use Apisearch\Repository\RepositoryReference;

/**
 * Class ElasticaWithAppIdWrapper.
 */
abstract class WithElasticaWrapper
{
    /**
     * @var ElasticaWrapper
     *
     * Elastica wrapper
     */
    protected $elasticaWrapper;

    /**
     * @var bool
     *
     * Refresh on write
     */
    protected $refreshOnWrite;

    /**
     * ElasticaSearchRepository constructor.
     *
     * @param ElasticaWrapper $elasticaWrapper
     * @param bool            $refreshOnWrite
     */
    public function __construct(
        ElasticaWrapper $elasticaWrapper,
        bool $refreshOnWrite
    ) {
        $this->elasticaWrapper = $elasticaWrapper;
        $this->refreshOnWrite = $refreshOnWrite;
    }

    /**
     * Refresh.
     *
     * @param RepositoryReference $repositoryReference
     */
    protected function refresh(RepositoryReference $repositoryReference)
    {
        $this
            ->elasticaWrapper
            ->refresh($repositoryReference);
    }

    /**
     * Normalize Repository Reference for cross index.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return RepositoryReference
     */
    protected function normalizeRepositoryReferenceCrossIndices(RepositoryReference $repositoryReference)
    {
        if (is_null($repositoryReference->getIndexUUID())) {
            return $repositoryReference;
        }

        $indices = $repositoryReference
            ->getIndexUUID()
            ->composeUUID();

        $appUUIDComposed = $repositoryReference
            ->getAppUUID()
            ->composeUUID();

        if ('*' === $indices) {
            return RepositoryReference::create(
                $appUUIDComposed,
                'all'
            );
        }

        $splittedIndices = explode(',', $indices);
        if (count($splittedIndices) > 1) {
            sort($splittedIndices);

            return RepositoryReference::create(
                $appUUIDComposed,
                implode('_', $splittedIndices)
            );
        }

        return $repositoryReference;
    }
}

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

namespace Apisearch\Plugin\QueryMapper\Tests\Functional\Mappers;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\ItemUUID;
use Apisearch\Plugin\QueryMapper\Domain\QueryMapper;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Tests\Functional\ApisearchServerBundleFunctionalTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SimpleQueryMapper.
 */
class SimpleQueryMapper implements QueryMapper
{
    /**
     * Get mapping tokens.
     *
     * @return string[]
     */
    public function getMappingTokens(): array
    {
        return ['query-mapped-simple'];
    }

    /**
     * Get mapped credentials.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference
    {
        return RepositoryReference::create(
            AppUUID::createById(ApisearchServerBundleFunctionalTest::$appId),
            IndexUUID::createById(ApisearchServerBundleFunctionalTest::$index)
        );
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return ApisearchServerBundleFunctionalTest::$godToken;
    }

    /**
     * Build query.
     *
     * @param Request $request
     *
     * @return Query
     */
    public function buildQueryByRequest(Request $request): Query
    {
        return Query::createByUUIDs([
            ItemUUID::createByComposedUUID('4~bike'),
            ItemUUID::createByComposedUUID('2~product'),
        ]);
    }
}

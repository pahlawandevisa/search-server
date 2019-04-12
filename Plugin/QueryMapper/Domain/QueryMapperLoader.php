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

namespace Apisearch\Plugin\QueryMapper\Domain;

use Apisearch\Http\Http;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryMapperLoader.
 */
class QueryMapperLoader
{
    /**
     * @var QueryMappers
     *
     * Query mappers
     */
    private $queryMappers;

    /**
     * Load query mappers.
     *
     * @param array $namespaces
     */
    public function __construct(array $namespaces)
    {
        $this->queryMappers = new QueryMappers();
        foreach ($namespaces as $namespace) {
            $this
                ->queryMappers
                ->addQueryMapper(new $namespace());
        }
    }

    /**
     * Having a Request query parameters, build a Query and fulfill credentials
     * if needed.
     *
     * @param Request $request
     */
    public function fulfillRequestWithQueryAndCredentials(Request $request)
    {
        $requestQuery = $request->query;
        $token = $requestQuery->get(Http::TOKEN_FIELD);
        if (empty($token)) {
            return;
        }

        $queryMapper = $this
            ->queryMappers
            ->findQueryMapperByToken($token);

        if (!$queryMapper instanceof QueryMapper) {
            return;
        }

        $repositoryReference = $queryMapper->getRepositoryReference();
        $requestQuery->set(Http::APP_ID_FIELD, $repositoryReference->getAppUUID()->composeUUID());
        $requestQuery->set(Http::INDEX_FIELD, $repositoryReference->getIndexUUID()->composeUUID());
        $requestQuery->set(Http::TOKEN_FIELD, $queryMapper->getToken());
        $requestQuery->set(Http::QUERY_FIELD, json_encode(
            $queryMapper->buildQueryByRequest($request)->toArray()
        ));
    }
}

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

namespace Apisearch\Server\Controller;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Controller\Extractor\RequestContentExtractor;
use Apisearch\Server\Domain\Query\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryController.
 */
class QueryController extends ControllerWithBus
{
    /**
     * Make a query.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidFormatException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $requestQuery = $request->query;
        $queryModel = RequestContentExtractor::extractQuery($request);

        $responseAsArray = $this
            ->commandBus
            ->handle(new Query(
                RepositoryReference::create(
                    AppUUID::createById($requestQuery->get(Http::APP_ID_FIELD, '')),
                    IndexUUID::createById($requestQuery->get(Http::INDEX_FIELD, '*'))
                ),
                $requestQuery->get(Http::TOKEN_FIELD, ''),
                $queryModel,
                array_filter($requestQuery->all(), function (string $key) {
                    return !in_array($key, [
                        Http::TOKEN_FIELD,
                        Http::APP_ID_FIELD,
                        Http::INDEX_FIELD,
                    ]);
                }, ARRAY_FILTER_USE_KEY)
            ))
            ->toArray();

        return new JsonResponse(
            $responseAsArray,
            200,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }
}

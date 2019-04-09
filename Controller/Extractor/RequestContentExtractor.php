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

namespace Apisearch\Server\Controller\Extractor;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\TransportableException;
use Apisearch\Http\Http;
use Apisearch\Query\Query;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestContentExtractor.
 */
class RequestContentExtractor
{
    /**
     * Get element from body, having a default value and a possible exception to
     * throw if this element is not accessible.
     *
     * @param Request                $request
     * @param string                 $field
     * @param TransportableException $exception
     * @param array                  $default
     *
     * @return array
     */
    public static function extractRequestContentObject(
        Request $request,
        string $field,
        TransportableException $exception,
        array $default = null
    ): array {
        $requestContent = $request->getContent();
        $requestBody = json_decode($requestContent, true);

        if (
            !empty($requestContent) &&
            is_null($requestBody)
        ) {
            throw $exception;
        }

        if (
            !is_array($requestBody) ||
            !isset($requestBody[$field]) ||
            !is_array($requestBody[$field])
        ) {
            if (is_null($default)) {
                throw $exception;
            }

            return $default;
        }

        return $requestBody[$field];
    }

    /**
     * Extract query from request.
     *
     * @param Request $request
     *
     * @return Query
     *
     * @throws InvalidFormatException
     */
    public static function extractQuery(Request $request): Query
    {
        $queryInQuery = $request
            ->query
            ->get(Http::QUERY_FIELD);

        if ($queryInQuery instanceof Query) {
            return $queryInQuery;
        }

        $queryAsArray = self::extractRequestContentObject(
            $request,
            Http::QUERY_FIELD,
            InvalidFormatException::queryFormatNotValid($request->getContent()),
            []
        );

        /*
         * We accept queries as well by GET in order to be able to cache them in
         * CDNs by using Cache headers
         */
        if ([] === $queryAsArray) {
            $possibleQuery = $request->query->get(Http::QUERY_FIELD);
            if (is_string($possibleQuery)) {
                $queryAsArray = self::decodeQuery($possibleQuery);
            }
        }

        $queryModel = Query::createFromArray($queryAsArray);
        $request
            ->query
            ->set(Http::QUERY_FIELD, $queryModel);

        return $queryModel;
    }

    /**
     * @param string $query
     *
     * @return array
     *
     * @throws InvalidFormatException
     */
    private static function decodeQuery(string $query): array
    {
        $response = \json_decode($query, true);
        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw InvalidFormatException::queryFormatNotValid($query);
        }

        return $response;
    }
}

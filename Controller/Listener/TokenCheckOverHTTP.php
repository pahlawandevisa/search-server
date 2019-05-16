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

namespace Apisearch\Server\Controller\Listener;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\InvalidTokenException;
use Apisearch\Http\Http;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Server\Controller\RequestAccessor;
use Apisearch\Server\Domain\Token\TokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class TokenCheckOverHTTP.
 */
class TokenCheckOverHTTP
{
    /**
     * @var TokenManager
     *
     * Token manager
     */
    private $tokenManager;

    /**
     * TokenValidationOverHTTP constructor.
     *
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Validate token given a Request.
     *
     * @param GetResponseEvent $event
     */
    public function checkTokenOnKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $query = $request->query;
        $headers = $request->headers;
        $token = $headers->get(
            Http::TOKEN_ID_HEADER,
            $query->get(
                Http::TOKEN_FIELD,
                ''
            )
        );

        if (is_null($token)) {
            throw InvalidTokenException::createInvalidTokenPermissions('');
        }

        $tokenString = $token instanceof Token
            ? $token->getTokenUUID()->composeUUID()
            : $token;

        $origin = $request->headers->get('Referer', '');
        $urlParts = parse_url($origin);
        $origin = $urlParts['host'] ?? '';
        $indices = $this->getIndices($request);
        $route = str_replace('apisearch_', '', $request->get('_route'));

        $token = $this
            ->tokenManager
            ->checkToken(
                AppUUID::createById($request->get('app_id', '')),
                $indices,
                TokenUUID::createById($tokenString),
                $origin,
                $route
            );

        if (!$request->attributes->has('app_id')) {
            $request
                ->attributes
                ->set('app_id', $token
                    ->getAppUUID()
                    ->composeUUID()
                );
        }

        if (!$request->attributes->has('index_id')) {
            $indicesAsString = array_map(function (IndexUUID $indexUUID) {
                return $indexUUID->composeUUID();
            }, $token->getIndices());

            $request
                ->attributes
                ->set('index_id', implode(',', $indicesAsString));
        }

        $request
            ->query
            ->set(Http::TOKEN_FIELD, $token);
    }

    /**
     * Get index taking in account multiquery.
     *
     * @param Request $request
     *
     * @return IndexUUID
     */
    private function getIndices(Request $request): IndexUUID
    {
        $query = null;
        $indices = [$request->get('index_id', '')];

        try {
            $query = RequestAccessor::extractQuery($request);
        } catch (InvalidFormatException $formatException) {
            return IndexUUID::createById($indices[0]);
        }

        foreach ($query->getSubqueries() as $subquery) {
            if ($subquery->getIndexUUID() instanceof IndexUUID) {
                $indices[] = $subquery->getIndexUUID()->getId();
            }
        }

        $indices = array_unique($indices);

        return IndexUUID::createById(implode(',', $indices));
    }
}
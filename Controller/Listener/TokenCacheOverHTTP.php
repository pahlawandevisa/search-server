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

use Apisearch\Http\Http;
use Apisearch\Model\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponsePromiseEvent;

/**
 * Class TokenCacheOverHTTP.
 */
class TokenCacheOverHTTP
{
    /**
     * Add cache control on kernel response.
     *
     * @param FilterResponsePromiseEvent $event
     */
    public function addCacheControlOnKernelAsyncResponse(FilterResponsePromiseEvent $event)
    {
        $event
            ->getPromise()
            ->then(function (Response $response) use ($event) {
                $request = $event->getRequest();
                $query = $request->query;
                $token = $query->get(Http::TOKEN_FIELD, '');

                if (
                    $request->isMethod(Request::METHOD_GET) &&
                    $token instanceof Token &&
                    $token->getTtl() > 0
                ) {
                    $response->setMaxAge($token->getTtl());
                    $response->setSharedMaxAge($token->getTtl());
                    $response->setPublic();
                } else {
                    $response->setMaxAge(0);
                    $response->setSharedMaxAge(0);
                    $response->setPrivate();
                }
            });
    }
}

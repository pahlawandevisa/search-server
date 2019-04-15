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

namespace Apisearch\Plugin\QueryMapper\Listener;

use Apisearch\Plugin\QueryMapper\Domain\QueryMapperLoader;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class CheckMappedQuery.
 */
class CheckMappedQuery
{
    /**
     * @var QueryMapperLoader
     *
     * Query mapper loader
     */
    private $queryMapperLoader;

    /**
     * CheckMappingQueries constructor.
     *
     * @param QueryMapperLoader $queryMapperLoader
     */
    public function __construct(QueryMapperLoader $queryMapperLoader)
    {
        $this->queryMapperLoader = $queryMapperLoader;
    }

    /**
     * On kernel request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->get('_route');

        if (!in_array($route, [
            'apisearch_v1_query',
            'apisearch_v1_query_all_indices',
        ])) {
            return;
        }

        $this
            ->queryMapperLoader
            ->fulfillRequestWithQueryAndCredentials(
                $event->getRequest()
            );
    }
}

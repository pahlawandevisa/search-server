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

use Apisearch\Model\Index;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\GetIndices;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GetIndicesController.
 */
class GetIndicesController extends ControllerWithBus
{
    /**
     * Get tokens.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $indices = $this
            ->commandBus
            ->handle(new GetIndices(
                RepositoryReference::create(
                    RequestAccessor::getAppUUIDFromRequest($request)
                ),
                RequestAccessor::getTokenFromRequest($request)
            ));

        return new JsonResponse(
            array_map(function (Index $index) {
                return $index->toArray();
            }, $indices),
            200,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }
}

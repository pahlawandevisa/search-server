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

use Apisearch\Config\Config;
use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\CreateIndex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateIndexController.
 */
class CreateIndexController extends ControllerWithBus
{
    /**
     * Create an index.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $configAsArray = RequestAccessor::extractRequestContentObject(
            $request,
            Http::CONFIG_FIELD,
            InvalidFormatException::configFormatNotValid($request->getContent()),
            []
        );

        $indexAsArray = RequestAccessor::extractRequestContentObject(
            $request,
            Http::INDEX_FIELD,
            InvalidFormatException::indexUUIDFormatNotValid(),
            []
        );

        $this
            ->commandBus
            ->handle(new CreateIndex(
                RepositoryReference::create(
                    RequestAccessor::getAppUUIDFromRequest($request)
                ),
                RequestAccessor::getTokenFromRequest($request),
                IndexUUID::createFromArray($indexAsArray),
                Config::createFromArray($configAsArray)
            ));

        return new JsonResponse('Index created', JsonResponse::HTTP_CREATED);
    }
}

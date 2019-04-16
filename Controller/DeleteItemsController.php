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
use Apisearch\Model\ItemUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteItems;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DeleteItemsController.
 */
class DeleteItemsController extends ControllerWithBus
{
    /**
     * Delete items.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidFormatException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $itemsUUIDAsArray = RequestAccessor::extractRequestContentObject(
            $request,
            '',
            InvalidFormatException::itemUUIDRepresentationNotValid($request->getContent()),
            []
        );

        $this
            ->commandBus
            ->handle(new DeleteItems(
                RepositoryReference::create(
                    RequestAccessor::getAppUUIDFromRequest($request),
                    RequestAccessor::getIndexUUIDFromRequest($request)
                ),
                RequestAccessor::getTokenFromRequest($request),
                array_map(function (array $object) {
                    return ItemUUID::createFromArray($object);
                }, $itemsUUIDAsArray)
            ));

        return new JsonResponse('Items deleted', $this->ok());
    }
}

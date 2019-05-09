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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Exception\TransportableException;
use Exception;
use React\Promise\FulfilledPromise;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponsePromiseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PHPExceptionToJsonResponse.
 */
class PHPExceptionToJsonResponse
{
    /**
     * When controller gets exception.
     *
     * @param GetResponsePromiseForExceptionEvent $event
     */
    public function onKernelAsyncException(GetResponsePromiseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof Exception) {
            $exception = $this->toOwnException($exception);
        }

        $event->setPromise(
            (new FulfilledPromise())
            ->then(function () use ($exception) {
                $exceptionErrorCode = $exception instanceof TransportableException
                    ? $exception::getTransportableHTTPError()
                    : 500;

                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'code' => $exceptionErrorCode,
                ], $exceptionErrorCode);
            })
        );

        $event->stopPropagation();
    }

    /**
     * To own exceptions.
     *
     * @param Exception $exception
     *
     * @return Exception
     */
    private function toOwnException(Exception $exception): Exception
    {
        if ($exception instanceof NotFoundHttpException) {
            preg_match('~No route found for "(.*)"~', $exception->getMessage(), $match);

            return ResourceNotAvailableException::routeNotAvailable($match[1] ?? $exception->getMessage());
        }

        return $exception;
    }
}

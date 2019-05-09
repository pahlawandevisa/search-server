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

namespace Apisearch\Server\React;

use Apisearch\ReactSymfonyServer\Adapter\KernelAdapter;
use Apisearch\Server\Exception\ErrorException;
use OneBundleApp\App\AppFactory;
use Symfony\Component\HttpKernel\AsyncKernel;

/**
 * Class Adapter.
 */
class Adapter implements KernelAdapter
{
    /**
     * Build kernel.
     *
     * @param string $environment
     * @param bool   $debug
     *
     * @return AsyncKernel
     *
     * @throws ErrorException
     */
    public static function buildKernel(
        string $environment,
        bool $debug
    ): AsyncKernel {
        $kernel = AppFactory::createApp(
            $appPath = dirname(__FILE__).'/..',
            $environment,
            $debug,
            true
        );

        if (!$kernel instanceof AsyncKernel) {
            throw new ErrorException('Kernel instance should be of type AsyncKernel. Check the kernel.');
        }

        return $kernel;
    }
}

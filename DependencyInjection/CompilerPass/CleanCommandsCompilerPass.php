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

namespace Apisearch\Server\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CleanCommandsCompilerPass.
 */
class CleanCommandsCompilerPass implements CompilerPassInterface
{
    /**
     * @var KernelInterface
     *
     * Kernel
     */
    private $kernel;

    /**
     * PluginsEnabledMiddlewareCompilerPass constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $container->removeDefinition('console.command.assets_install');

        if (!$this->kernel->isDebug()) {
            $container->removeDefinition('console.command.config_dump_reference');
            $container->removeDefinition('console.command.container_debug');
            $container->removeDefinition('console.command.debug_autowiring');
            $container->removeDefinition('console.command.event_dispatcher_debug');
            $container->removeDefinition('console.command.config_debug');
            $container->removeDefinition('console.command.router_debug');
            $container->removeDefinition('console.command.router_match');
            $container->removeDefinition('console.command.xliff_lint');
            $container->removeDefinition('console.command.yaml_lint');
            $container->removeDefinition('tactician.command.debug');
        }
    }
}

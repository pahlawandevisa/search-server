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

namespace Apisearch\Plugin\RabbitMQ\Tests\Functional;

use Apisearch\Server\Tests\Functional\Domain\Repository\AsynchronousCommandsTest as BaseAsynchronousCommandsTest;

/**
 * Class AsynchronousCommandsTest.
 */
class AsynchronousCommandsTest extends BaseAsynchronousCommandsTest
{
    /**
     * Force all connections to be dropped.
     */
    protected function dropConnections()
    {
        $this->markTestSkipped('RabbitMQ implementation needs drop-connection implementation');
    }

    use RabbitMQTestTrait;
}

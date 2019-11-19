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

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Server\Exception\QueuePluginException;
use Apisearch\Server\Tests\Functional\HttpFunctionalTest;

/**
 * Class QueuesCommandsPluginNotFoundTest.
 */
class QueuesCommandsPluginNotFoundTest extends HttpFunctionalTest
{
    /**
     * Decorate configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    protected static function decorateConfiguration(array $configuration): array
    {
        $configuration['apisearch_server']['commands_adapter'] = 'enqueue';
        return $configuration;
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass()
    {
        try {
            static::$kernel = static::getKernel();
            static::$kernel->boot();
            self::fail('Exception expected');
        } catch (QueuePluginException $e) {
            // Silent pass
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public static function tearDownAfterClass()
    {
        //
    }
}

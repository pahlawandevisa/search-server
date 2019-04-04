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

namespace Apisearch\Server\Tests\Functional\Domain\Repository;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Server\Tests\Functional\AsynchronousFunctionalTest;

/**
 * Class AsynchronousCommandsTest.
 */
abstract class AsynchronousCommandsTest extends AsynchronousFunctionalTest
{
    use AllAsynchronousTests;

    /**
     * Test simple query.
     *
     * We start sleeping 2 seconds to make sure that the commands are properly
     * ingested and processed by the command consumer
     */
    public function testSimpleQuery()
    {
        sleep(1);
        $this->assertCount(
            5,
            $this
                ->query(Query::createMatchAll())
                ->getItems()
        );
    }

    /**
     * Test connection persistence. The given queues system should be "forced"
     * to remove all active connections, emulating some kind of problem in the
     * networking or in the external services.
     *
     * After n seconds, we should be able to continue serving properly and the
     * queues should be able to continuing making proper ingestion.
     */
    public function testConnectionPersistence()
    {
        $this->dropConnections();
        sleep(5);
        static::indexItems([
            Item::create(new ItemUUID('888', 'item')),
        ]);

        $this->assertCount(6, $this
            ->query(Query::createMatchAll())
            ->getItems()
        );

        static::resetScenario();
    }
}

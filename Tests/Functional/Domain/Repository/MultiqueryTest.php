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

use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Trait MultiqueryTest.
 */
trait MultiqueryTest
{
    /**
     * Test simple multiquery.
     */
    public function testSimpleMultiQuery()
    {
        $query = Query::createMultiquery([
            'q1' => Query::create('alfaguarra')->identifyWith('123'),
            'q2' => Query::create('boosting')->identifyWith('456'),
        ]);

        /**
         * @var Result
         */
        $result = $this->query($query);
        $subresults = $result->getSubresults();
        $this->assertCount(2, $subresults);
        $this->assertEquals('123', $subresults['q1']->getQueryUUID());
        $this->assertEquals(1, $subresults['q1']->getTotalHits());
        $this->assertEquals('456', $subresults['q2']->getQueryUUID());
        $this->assertEquals(3, $subresults['q2']->getTotalHits());
    }
}

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

namespace Apisearch\Plugin\GenSearch\Tests\Unit\Domain;

use Apisearch\Plugin\GenSearch\Domain\InMemorySpeciesRepository;
use Apisearch\Plugin\GenSearch\Domain\SpeciesMutator;
use Apisearch\Query\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class SpeciesMutatorTest
 */
class SpeciesMutatorTest extends TestCase
{
    /**
     * Test minimum bit mutation of a query
     */
    public function testBitMutation()
    {
        $emptyQuery = Query::createMatchAll();
        $queryMutator = new SpeciesMutator(new InMemorySpeciesRepository());
        $queryMutator->createSimpleBitMutation($emptyQuery);
        $this->assertEmpty($emptyQuery->getFilterFields());
        $emptyQuery = Query::createMatchAll()
            ->setFilterFields([
                'field1'
            ]);
        $queryMutator->createSimpleBitMutation($emptyQuery);
        $filterFields = $emptyQuery->getFilterFields();
        $this->assertTrue(in_array($filterFields[0], [
            'field1^2',
            'field1^0'
        ]));
        $emptyQuery = Query::createMatchAll()
            ->setFilterFields([
                'field1',
                'field2',
            ]);
        $queryMutator->createSimpleBitMutation($emptyQuery);
        $filterFields = $emptyQuery->getFilterFields();
        $this->assertFalse(
            $filterFields[0] == 'field1' &&
            $filterFields[1] == 'field2'
        );
    }
}
<?php
/*
 * This file is part of the {Package name}.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Apisearch\Plugin\GenSearch\Tests\Functional;

use Apisearch\Query\Query;

/**
 * Class BasicTest
 */
class BasicTest extends GenSearchFunctionalTest
{
    /**
     * Test basic behavior
     */
    public function testEmpty()
    {
        $this->assertCount(5, $this->query(Query::createMatchAll())->getItems());
    }

    /**
     * Let's make a mutation
     */
    public function testSimpleMutation()
    {
        $o =static::runCommand([
            'command' => 'apisearch-plugin:gen-search:generate-mutation',
        ]);
echo $o;
        $this->assertCount(5, $this->query(Query::createMatchAll())->getItems());
    }
}
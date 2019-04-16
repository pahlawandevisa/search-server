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

use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;
use Apisearch\User\Interaction;

/**
 * Class InteractionsTest.
 */
trait InteractionsTest
{
    /**
     * test that we can send an interaction.
     */
    public function testAddInteraction()
    {
        $this->addInteraction(
            new Interaction(
                new User('123'),
                new ItemUUID('1', 'product'),
                'click',
                [
                    'position' => 1,
                ]
            )
        );

        $this->assertTrue(true);
    }
}

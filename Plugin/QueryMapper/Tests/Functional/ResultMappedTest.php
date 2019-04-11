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

namespace Apisearch\Plugin\QueryMapper\Tests\Functional;

/**
 * Class ResultMappedTest.
 */
class ResultMappedTest extends QueryMapperFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $client = static::createClient();
        $client->request(
            'get',
            sprintf('/v1?app_id=%s&index=%s&token=%s',
                static::$appId,
                static::$index,
                static::$readonlyToken
            )
        );

        $resultAsJson = $client->getResponse()->getContent();
        $resultAsArray = json_decode($resultAsJson, true);
        $this->assertEquals([
            'item_nb' => 5,
            'item_ids' => [
                '1~product',
                '2~product',
                '3~book',
                '4~bike',
                '5~gum',
            ],
        ], $resultAsArray);
    }
}

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

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Class QueryMappedTest.
 */
class QueryMappedTest extends QueryMapperFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testWithMappedQuery()
    {
        $client = static::createClient();
        $client->request(
            'get',
            sprintf('/v1/%s?token=%s', static::$appId, 'query-mapped-simple')
        );

        $resultAsJson = $client->getResponse()->getContent();
        $result = Result::createFromArray(json_decode($resultAsJson, true));
        $this->assertEquals(2, $result->getTotalHits());
        $this->assertEquals('2~product', $result->getItems()[0]->composeUUID());
        $this->assertEquals('4~bike', $result->getItems()[1]->composeUUID());
    }

    /**
     * Test without mapped query.
     */
    public function testWithoutMappedQuery()
    {
        $client = static::createClient();
        $client->request(
            'get',
            sprintf('/v1/%s?token=%s', static::$appId, 'non-existing')
        );

        $resultAsJson = $client->getResponse()->getContent();
        $resultAsArray = json_decode($resultAsJson, true);
        $this->assertEquals(401, $resultAsArray['code']);
    }

    /**
     * Test another endpoint.
     */
    public function testAnotherEndpoint()
    {
        $client = static::createClient();
        $client->request(
            'put',
            sprintf('/v1/%s/indices/%s/items?token=%s',
                static::$appId,
                static::$index,
                static::$godToken
            ),
            [], [], [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                Item::create(
                    ItemUUID::createByComposedUUID('10~lele')
                )->toArray(),
            ])
        );

        $this->assertCount(6, $this
            ->query(Query::createMatchAll())
            ->getItems()
        );
    }
}

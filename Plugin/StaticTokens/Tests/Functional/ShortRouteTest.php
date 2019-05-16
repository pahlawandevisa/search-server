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

namespace Apisearch\Plugin\StaticTokens\Tests\Functional;

use Apisearch\Http\Http;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShortRouteTest.
 */
class ShortRouteTest extends StaticTokensFunctionalTest
{
    /**
     * Test custom route.
     */
    public function testCustomRoute()
    {
        $request = new Request();
        $request->setMethod('GET');
        $request->server->set('REQUEST_URI', '/v1');
        $request->headers->set(Http::TOKEN_ID_HEADER, 'onlyindex');

        $response = static::$kernel->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
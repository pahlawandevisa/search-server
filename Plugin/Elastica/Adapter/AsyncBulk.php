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

namespace Apisearch\Plugin\Elastica\Adapter;

use Elastica\Bulk as ElasticaBulk;
use Elastica\Request;
use Elastica\Response;
use React\Promise\PromiseInterface;

/**
 * Class AsyncBulk.
 */
class AsyncBulk extends ElasticaBulk
{
    /**
     * Send bulk async.
     *
     * @return PromiseInterface<\Elastica\Bulk\ResponseSet>
     */
    public function sendAsync()
    {
        $path = $this->getPath();
        $data = $this->toString();

        return $this
            ->_client
            ->requestAsync(
                $path,
                Request::POST,
                $data,
                $this->_requestParams,
                Request::NDJSON_CONTENT_TYPE
            )
            ->then(function (Response $response) {
                return $this->_processResponse($response);
            });
    }
}

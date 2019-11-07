<?php

namespace Apisearch\Plugin\Elastica\Domain\Polyfill;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Type.
 */
class Type
{
    /**
     * Set type to endpoint.
     *
     * @param AbstractEndpoint $endpoint
     * @param string           $version
     */
    public static function setEndpointType(
        AbstractEndpoint $endpoint,
        string $version
    ) {
        if ('6' == $version) {
            $endpoint->setType('item');
        }
    }
}

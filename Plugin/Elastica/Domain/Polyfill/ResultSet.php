<?php

namespace Apisearch\Plugin\Elastica\Domain\Polyfill;

use Elastica\ResultSet as ElasticaResultSet;

/**
 * Class ResultSet.
 */
class ResultSet
{
    public static function getTotalHits(ElasticaResultSet $resultSet)
    {
        $data = $resultSet
            ->getResponse()
            ->getData();

        if (!isset($data['hits'])) {
            return 0;
        }

        $totalHits = $data['hits']['total'];

        return \intval(is_array($totalHits)
            ? $totalHits['value']
            : $totalHits);
    }
}

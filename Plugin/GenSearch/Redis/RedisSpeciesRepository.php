<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Redis;

use Apisearch\Plugin\GenSearch\Domain\SpeciesCollection;
use Apisearch\Plugin\GenSearch\Domain\SpeciesRepository;
use Apisearch\Plugin\Redis\Domain\RedisWrapper;

/**
 * Class RedisSpeciesRepository
 */
class RedisSpeciesRepository implements SpeciesRepository
{
    /**
     * @var RedisWrapper
     *
     * Redis wrapper
     */
    private $redisWrapper;

    /**
     * Get all alive species
     *
     * @return SpeciesCollection
     */
    public function getAliveSpecies() : SpeciesCollection
    {
        $data = $this
            ->redisWrapper
            ->getClient()
            ->get('gen_search_alive_species');
    }
}
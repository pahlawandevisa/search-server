<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Redis;

use Apisearch\Plugin\GenSearch\Domain\Species;
use Apisearch\Plugin\GenSearch\Domain\SpeciesCollection;
use Apisearch\Plugin\GenSearch\Domain\SpeciesRepository;
use Apisearch\Plugin\Redis\Domain\RedisWrapper;
use Apisearch\Query\Query;

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
    public function getAliveSpecies(): SpeciesCollection
    {
        // TODO: Implement getAliveSpecies() method.
    }

    /**
     * Put species
     *
     * @param Species $species
     */
    public function putSpecies(Species $species)
    {
        // TODO: Implement addNewSpecies() method.
    }

    /**
     * Record Query for species
     *
     * @param Species $species
     * @param Query   $query
     */
    public function recordQueryForSpecies(
        Species $species,
        Query $query
    )
    {
        // TODO: Implement recordQueryForSpecies() method.
    }

    /**
     * Increase by 1 an event
     *
     * @param string $queryUUID
     * @param string $eventName
     */
    public function increaseQueryEvent(
        string $queryUUID,
        string $eventName
    )
    {
        // TODO: Implement increaseQueryEvent() method.
    }

    /**
     * Exterminate Species
     *
     * @param Species $species
     */
    public function exterminateSpecies(Species $species)
    {
        // TODO: Implement exterminateSpecies() method.
    }

    /**
     * Put species
     *
     * @param string $UUID
     *
     * @return Species
     */
    public function findSpecies(string $UUID): ? Species
    {

    }
}
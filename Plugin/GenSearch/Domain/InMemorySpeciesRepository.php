<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;

use Apisearch\Query\Query;

/**
 * Class InMemorySpeciesRepository
 */
class InMemorySpeciesRepository implements SpeciesRepository
{
    /**
     * @var Query[]
     *
     * Queries
     */
    private $queries = [];

    /**
     * @var Species[]
     *
     * Species
     */
    private $species = [];

    /**
     * @var string[]
     *
     * Blacklist Species
     */
    private $blacklistSpecies = [];

    /**
     * Get all alive species
     *
     * @return SpeciesCollection
     */
    public function getAliveSpecies() : SpeciesCollection
    {
        $speciesCollection = new SpeciesCollection();
        foreach ($this->species as $species) {
            $speciesCollection->addSpecie($species);
        }

        return $speciesCollection;
    }

    /**
     * Put species
     *
     * @param Species $species
     */
    public function putSpecies(Species $species)
    {
        $this->species[$species->getUUID()] = $species;
    }

    /**
     * Record Query for species
     *
     * @param Species $species
     * @param Query $query
     */
    public function recordQueryForSpecies(
        Species $species,
        Query $query
    )
    {
        $this->queries[$query->getUUID()] = $species->getUUID();
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
        return $this->species[$UUID] ?? null;
    }
}
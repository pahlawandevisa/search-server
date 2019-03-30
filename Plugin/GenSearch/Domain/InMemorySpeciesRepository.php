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
     * Add new species
     *
     * @param Species $species
     */
    public function addNewSpecies(Species $species)
    {
        $this->species[$species->getUUID()] = $species;
    }

    /**
     * Record Query
     *
     * @param Query $query
     */
    public function recordQuery(Query $query)
    {
        $this->queries[$query->getUUID()] = $query;
    }

    /**
     * Increase by 1 an event
     *
     * @param Query  $query
     * @param string $eventName
     */
    public function increaseQueryEvent(
        Query $query,
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
}
<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;
use Apisearch\Query\Query;

/**
 * Interface SpeciesRepository
 */
interface SpeciesRepository
{
    /**
     * Get all alive species
     *
     * @return SpeciesCollection
     */
    public function getAliveSpecies() : SpeciesCollection;

    /**
     * Add new species
     *
     * @param Species $species
     */
    public function addNewSpecies(Species $species);

    /**
     * Record Query
     *
     * @param Query $query
     */
    public function recordQuery(Query $query);

    /**
     * Increase by 1 an event
     *
     * @param Query $query
     * @param string $eventName
     */
    public function increaseQueryEvent(
        Query $query,
        string $eventName
    );

    /**
     * Exterminate Species
     *
     * @param Species $species
     */
    public function exterminateSpecies(Species $species);
}
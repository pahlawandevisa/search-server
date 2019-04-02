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
     * Put species
     *
     * @param Species $species
     */
    public function putSpecies(Species $species);

    /**
     * Put species
     *
     * @param string $UUID
     *
     * @return Species
     */
    public function findSpecies(string $UUID): ? Species;

    /**
     * Record Query for species
     *
     * @param Species $species
     * @param Query $query
     */
    public function recordQueryForSpecies(
        Species $species,
        Query $query
    );

    /**
     * Increase by 1 an event
     *
     * @param string $queryUUID
     * @param string $eventName
     */
    public function increaseQueryEvent(
        string $queryUUID,
        string $eventName
    );

    /**
     * Exterminate Species
     *
     * @param Species $species
     */
    public function exterminateSpecies(Species $species);
}
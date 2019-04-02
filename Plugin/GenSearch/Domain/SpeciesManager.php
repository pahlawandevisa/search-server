<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;
use Apisearch\Query\Query;

/**
 * Class SpeciesManager
 */
class SpeciesManager
{
    /**
     * @var SpeciesRepository
     *
     * Species Repository
     */
    private $speciesRepository;

    /**
     * SpeciesChooser constructor.
     *
     * @param SpeciesRepository $speciesRepository
     */
    public function __construct(SpeciesRepository $speciesRepository)
    {
        $this->speciesRepository = $speciesRepository;
    }

    /**
     * Given a query, apply neede genetic changes.
     *
     * @param Query $query
     *
     * @return Query
     */
    public function applyGeneticChange(Query $query) : Query
    {
        $chosenSpecies = $this->chooseOneValidSpecies();
        if (!$chosenSpecies instanceof Species) {
            return $query;
        }

        $this
            ->speciesRepository
            ->recordQueryForSpecies(
                $chosenSpecies,
                $query
            );

        $this->injectQueryDNAToQuery(
            $query,
            $chosenSpecies->getQuery()
        );

        return $query;
    }

    /**
     * Retrieve valid species
     *
     * @return Species|null
     */
    public function chooseOneValidSpecies() : ? Species
    {
        return $this
            ->speciesRepository
            ->getAliveSpecies()
            ->getRandomSpecies();
    }

    /**
     * Save species
     *
     * @param Species $species
     */
    public function saveSpecies(Species $species)
    {
        $this
            ->speciesRepository
            ->putSpecies($species);
    }

    /**
     * Replace query with new DNA
     *
     * @param Query $query
     * @param Query $patternQuery
     */
    private function injectQueryDNAToQuery(
        Query $query,
        Query $patternQuery
    )
    {
        $query->setFilterFields($patternQuery->getFilterFields());
        $query->setFuzziness($patternQuery->getFuzziness());
        $query->setScoreStrategies($patternQuery->getScoreStrategies());
    }
}
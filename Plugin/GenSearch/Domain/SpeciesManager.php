<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;
use Apisearch\Model\Index;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Repository\AppRepository\IndexRepository;

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
     * @var IndexRepository
     *
     * Index repository
     */
    private $indexRepository;

    /**
     * SpeciesManager constructor.
     *
     * @param SpeciesRepository $speciesRepository
     * @param IndexRepository $indexRepository
     */
    public function __construct(
        SpeciesRepository $speciesRepository,
        IndexRepository $indexRepository
    )
    {
        $this->speciesRepository = $speciesRepository;
        $this->indexRepository = $indexRepository;
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

    /**
     * Load all valid searchable fields given a Species, and filter by these
     * fields that still exists and these that are willing to be used
     *
     * @param Query $query
     */
    public function setValidSearchableFieldsFromQuery(Query $query)
    {
        $fields = $query->getFilterFields();
    }

    /**
     * Load all existing searchable fields
     *
     * @return string[]
     */
    public function getAllSearchableFields() : array
    {
        $indices = $this
            ->indexRepository
            ->getIndices();

        return array_reduce($indices, function(array $fields, Index $index) {
            return array_merge(
                $fields,
                array_filter(
                    $index->getFields(),
                    function(string $field) {
                        return strpos('searchable_metadata.', $field) === 0;
                    }
                )
            );
        }, [
            'exact_matching_metadata'
        ]);
    }
}
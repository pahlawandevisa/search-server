<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;

/**
 * Class SpeciesChooser
 */
class SpeciesChooser
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
     * Retrieve valid species
     *
     * @return Species
     */
    public function chooseOneValidSpecies() : Species
    {
        return $this
            ->speciesRepository
            ->getAliveSpecies()
            ->getRandomSpecies();
    }
}
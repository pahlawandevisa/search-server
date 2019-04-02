<?php

/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain\CommandHandler;

use Apisearch\Plugin\GenSearch\Domain\Command\GenerateMutation;
use Apisearch\Plugin\GenSearch\Domain\Species;
use Apisearch\Plugin\GenSearch\Domain\SpeciesManager;
use Apisearch\Plugin\GenSearch\Domain\SpeciesMutator;

/**
 * Class GenerateMutationHandler
 */
class GenerateMutationHandler
{
    /**
     * @var SpeciesManager
     *
     * Species manager
     */
    private $speciesManager;

    /**
     * @var SpeciesMutator
     *
     * Species mutator
     */
    private $speciesMutator;

    /**
     * GenerateMutationHandler constructor.
     *
     * @param SpeciesManager $speciesManager
     * @param SpeciesMutator $speciesMutator
     */
    public function __construct(
        SpeciesManager $speciesManager,
        SpeciesMutator $speciesMutator
    )
    {
        $this->speciesManager = $speciesManager;
        $this->speciesMutator = $speciesMutator;
    }


    /**
     * Generate mutation
     *
     * @param GenerateMutation $generateMutation
     */
    public function handle(GenerateMutation $generateMutation)
    {
        $chosenSpecies = $this
            ->speciesManager
            ->chooseOneValidSpecies();

        if (!$chosenSpecies instanceof Species) {
            return;
        }

        $this
            ->speciesMutator
            ->mutateAnSpecies($chosenSpecies);

        $this
            ->speciesManager
            ->saveSpecies($chosenSpecies);
    }
}
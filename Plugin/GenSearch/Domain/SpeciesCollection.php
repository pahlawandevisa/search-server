<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;

use DateTime;

/**
 * Class SpecyCollection
 */
class SpeciesCollection
{
    /**
     * @var Species[]
     *
     * Species
     */
    private $species = [];

    /**
     * Add species
     *
     * @param Species
     */
    public function addSpecie(Species $species)
    {
        $this->species[] = $species;
    }

    /**
     * Get random valid species
     *
     * @return Species|null
     */
    public function getRandomSpecies() : ? Species
    {
        $speciesTable = [];
        $maxSecondsActive = 0;
        $roofSecondsActive = 31556926;

        foreach ($this->species as $species) {
            $secondsActive = $this->calculateSecondsActive($species);
            $maxSecondsActive = max($secondsActive, $maxSecondsActive, $roofSecondsActive);
            $speciesTable[$species->getUUID()] = [
                'species' => $species,
                'uuid' => $species->getUUID(),
                'punct' => $this->calculateSpeciesPunctuation($species),
                'seconds_active' => $secondsActive
            ];
        }

        if (empty($speciesTable)) {
            return null;
        }

        return $this->pickWeightedRandomSpecies($speciesTable);
    }

    /**
     * Get punctuation by Species
     *
     * @param Species $species
     *
     * @return int
     */
    private function calculateSpeciesPunctuation(Species $species) : int
    {
        $speciesEvents = $species->getEvents();

        return (
            ($speciesEvents['click_position_1'] ?? 0) * 10 +
            ($speciesEvents['click_position_2'] ?? 0) * 8 +
            ($speciesEvents['click_position_3'] ?? 0) * 6 +
            ($speciesEvents['click_position_4'] ?? 0) * 4 +
            ($speciesEvents['click_position_5'] ?? 0) * 2
        ) / (($speciesEvents['queries'] ?? 0) + 1);
    }

    /**
     * Get seconds active
     *
     * @param Species $species
     *
     * @return int
     */
    private function calculateSecondsActive(Species $species) : int
    {
        return (new DateTime)->diff($species->getCreatedAt())->s;
    }

    /**
     * Pick a random item based on weights.
     *
     * @param Species[] $species
     *
     * @return Species
     */
    function pickWeightedRandomSpecies(array $species) : Species
    {
        $count = count($species);
        $weights = array_column($species, 'seconds_active', 'uuid');
        $i = $n = 0;
        $num = mt_rand(0, array_sum($weights));
        while($i < $count){
            $n += $weights[$i];
            if($n >= $num){
                break;
            }
            $i++;
        }

        return $species[$i];
    }
}
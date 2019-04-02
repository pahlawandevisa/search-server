<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\GenSearch\Domain;

use Apisearch\Query\Query;
use Ramsey\Uuid\Uuid;
use DateTime;

/**
 * Class SpeciesMutator
 */
class SpeciesMutator
{
    /**
     * Create a new mutation for a species
     *
     * @param Species $species
     *
     * @return Species
     */
    public function mutateAnSpecies(Species $species)
    {
        $speciesQuery = $species->getQuery();
        $query = clone $speciesQuery;
        $this->createSimpleBitMutation($query);

        $mutatedSpecies = new Species(
            Uuid::uuid4()->toString(),
            $species->getUUID(),
            $query,
            new DateTime
        );
        echo 'Created Mutation #'. $mutatedSpecies->getUUID() . PHP_EOL;
        print_r($mutatedSpecies->getQuery()->getFilterFields());

        return $mutatedSpecies;
    }

    /**
     * Create a simple bit mutation on a field
     *
     * @param Query $query
     */
    public function createSimpleBitMutation(Query $query)
    {
        $searchableFields = $query->getFilterFields();
        $searchableFieldsArray = array_map(function(string $field) {
            $parts = explode('^', $field, 2);
            return [$parts[0], (int) ($parts[1] ?? 1)];
        }, $searchableFields);
        if (!empty($searchableFieldsArray)) {
            $randomKey = array_rand($searchableFieldsArray);
            $searchableFieldsArray[$randomKey][1] = max(
                0,
                rand(0, 1)
                    ? --$searchableFieldsArray[$randomKey][1]
                    : ++$searchableFieldsArray[$randomKey][1]
            );
            $query->setFilterFields(array_map(function (array $parts) {
                return implode('^', $parts);
            }, $searchableFieldsArray));
        }
    }
}
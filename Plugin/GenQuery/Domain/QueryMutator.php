<?php

/*
 * This file is part of the {Package name}.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Apisearch\Plugin\GenQuery\Domain;

use Apisearch\Query\Query;

/**
 * Class QueryMutator
 */
class QueryMutator
{
    /**
     * Create a new mutation for a query
     *
     * @param Query $query
     */
    public function mutateAQuery(Query $query)
    {
        $this->createSimpleBitMutation($query);
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
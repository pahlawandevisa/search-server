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

namespace Apisearch\Plugin\QueryMapper\Domain;

use Apisearch\Model\TokenUUID;
use Apisearch\Result\Result;

/**
 * Class ResultMapperLoader.
 */
class ResultMapperLoader
{
    /**
     * @var ResultMappers
     *
     * Result mappers
     */
    private $resultMappers;

    /**
     * Load query mappers.
     *
     * @param array $namespaces
     */
    public function __construct(array $namespaces)
    {
        $this->resultMappers = new ResultMappers();
        foreach ($namespaces as $namespace) {
            $this
                ->resultMappers
                ->addResultMapper(new $namespace());
        }
    }

    /**
     * Having a Result object, if needed, transform it into a regular array.
     *
     * @param TokenUUID $tokenUUID
     * @param Result    $result
     *
     * @return array|null
     */
    public function getArrayFromResult(
        TokenUUID $tokenUUID,
        Result $result
    ): ? array {
        $resultMapper = $this
            ->resultMappers
            ->findResultMapperByToken($tokenUUID->composeUUID());

        if (!$resultMapper instanceof ResultMapper) {
            return null;
        }

        return $resultMapper->buildArrayFromResult($result);
    }
}

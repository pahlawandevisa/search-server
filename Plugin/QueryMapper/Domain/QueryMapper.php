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

use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryMapper.
 */
interface QueryMapper
{
    /**
     * Get mapping tokens.
     *
     * @return string[]
     */
    public function getMappingTokens(): array;

    /**
     * Get mapped credentials.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference;

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Build query.
     *
     * @param Request $request
     *
     * @return Query
     */
    public function buildQueryByRequest(Request $request): Query;
}

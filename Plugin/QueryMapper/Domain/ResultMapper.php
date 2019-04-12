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

use Apisearch\Result\Result;

/**
 * Class ResultMapper.
 */
interface ResultMapper
{
    /**
     * Get tokens.
     *
     * @return string[]
     */
    public function getTokens(): array;

    /**
     * Build array.
     *
     * @param Result $result
     *
     * @return array
     */
    public function buildArrayFromResult(Result $result): array;
}

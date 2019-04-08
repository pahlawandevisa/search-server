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

namespace Apisearch\Server\Domain\Token;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;

/**
 * Class CredentialsTokenValidator.
 */
class CredentialsTokenValidator implements TokenValidator
{
    /**
     * Validate token given basic fields.
     *
     * If is valid, return valid Token
     *
     * @param AppUUID   $appUUID
     * @param IndexUUID $indexUUID
     * @param Token     $token
     * @param string    $referrer
     * @param string    $path
     * @param string    $verb
     *
     * @return bool
     */
    public function isTokenValid(
        Token $token,
        AppUUID $appUUID,
        IndexUUID $indexUUID,
        string $referrer,
        string $path,
        string $verb
    ): bool {
        $endpoint = strtolower($verb.'~~'.trim($path, '/'));
        $indexUUIDAsStringArray = $this->indexUUIDArrayToStringArray([$indexUUID]);
        $tokenIndexUUIDAsStringArray = $this->indexUUIDArrayToStringArray($token->getIndices());

        return
            ($token instanceof Token) &&
            (
                $appUUID->composeUUID() === $token->getAppUUID()->composeUUID()
            ) &&
            (
                empty($token->getIndices()) ||
                empty(array_diff(
                    $indexUUIDAsStringArray,
                    $tokenIndexUUIDAsStringArray
                ))
            ) &&
            (
                empty($token->getEndpoints()) ||
                in_array($endpoint, $token->getEndpoints())
            )
        ;
    }

    /**
     * Given an array of indices, return all indexUUID as an array of strings.
     *
     * @param IndexUUID[] $indexUUIDs
     *
     * @return string[]
     */
    private function indexUUIDArrayToStringArray(array $indexUUIDs): array
    {
        $indexUUIDStrings = [];
        foreach ($indexUUIDs as $indexUUID) {
            $indexUUIDStrings = array_merge(
                $indexUUIDStrings,
                array_filter(
                    explode(
                        ',',
                        trim($indexUUID->composeUUID(), ',* ')
                    )
                )
            );
        }

        return $indexUUIDStrings;
    }
}

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

namespace Apisearch\Server\Domain\Repository\AppRepository;

use Apisearch\Model\AppUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Token\TokenLocator;

/**
 * Class InMemoryTokenRepository.
 */
class InMemoryTokenRepository implements TokenRepository, TokenLocator
{
    /**
     * @var array[]
     *
     * Tokens
     */
    private $tokens = [];

    /**
     * Locator is enabled.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * Add token.
     *
     * @param Token               $token
     * @param RepositoryReference $repositoryReference
     */
    public function addToken(
        RepositoryReference $repositoryReference,
        Token $token
    ) {
        $appUUIDComposed = $repositoryReference
            ->getAppUUID()
            ->composeUUID();

        if (!isset($this->tokens[$appUUIDComposed])) {
            $this->tokens[$appUUIDComposed] = [];
        }

        $this->tokens[$appUUIDComposed][$token->getTokenUUID()->composeUUID()] = $token;
    }

    /**
     * Delete token.
     *
     * @param TokenUUID           $tokenUUID
     * @param RepositoryReference $repositoryReference
     */
    public function deleteToken(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    ) {
        $appUUIDComposed = $repositoryReference
            ->getAppUUID()
            ->composeUUID();

        if (!isset($this->tokens[$appUUIDComposed])) {
            return;
        }

        unset($this->tokens[$appUUIDComposed][$tokenUUID->composeUUID()]);
    }

    /**
     * Get tokens.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Token[]
     */
    public function getTokens(RepositoryReference $repositoryReference): array
    {
        $appUUIDComposed = $repositoryReference
            ->getAppUUID()
            ->composeUUID();

        if (!isset($this->tokens[$appUUIDComposed])) {
            return [];
        }

        return $this->tokens[$appUUIDComposed];
    }

    /**
     * Delete all tokens.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function deleteTokens(RepositoryReference $repositoryReference)
    {
        $appUUIDComposed = $repositoryReference
            ->getAppUUID()
            ->composeUUID();

        unset($this->tokens[$appUUIDComposed]);
    }

    /**
     * Get token by uuid.
     *
     * @param AppUUID   $appUUID
     * @param TokenUUID $tokenUUID
     *
     * @return Token|null
     */
    public function getTokenByUUID(
        AppUUID $appUUID,
        TokenUUID $tokenUUID
    ): ? Token {
        $appUUIDComposed = $appUUID->composeUUID();

        if (!isset($this->tokens[$appUUIDComposed])) {
            return null;
        }

        return $this->tokens[$appUUIDComposed][$tokenUUID->composeUUID()] ?? null;
    }
}

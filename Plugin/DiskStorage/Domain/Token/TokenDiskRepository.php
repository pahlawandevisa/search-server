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

namespace Apisearch\Plugin\DiskStorage\Domain\Token;

use Apisearch\Model\AppUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Plugin\DiskStorage\Domain\Storage;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Repository\AppRepository\TokenRepository;
use Apisearch\Server\Domain\Token\TokenLocator;
use Apisearch\Server\Domain\Token\TokenProvider;
use React\Promise\PromiseInterface;

/**
 * Class TokenDiskRepository.
 */
class TokenDiskRepository implements TokenRepository, TokenLocator, TokenProvider
{
    /**
     * @var Storage
     *
     * Storage
     */
    private $storage;

    /**
     * @var bool
     *
     * Enabled
     */
    private $enabled;

    /**
     * TokenDiskRepository constructor.
     *
     * @param Storage $storage
     * @param bool    $enabled
     */
    public function __construct(Storage $storage, bool $enabled)
    {
        $this->storage = $storage;
        $this->enabled = $enabled;
    }

    /**
     * Add token.
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     *
     * @return PromiseInterface
     */
    public function addToken(
        RepositoryReference $repositoryReference,
        Token $token
    ): PromiseInterface {
        return $this
            ->storage
            ->set(
                $this->composeKey($repositoryReference->getAppUUID()),
                $token->getTokenUUID()->composeUUID(),
                $token->toArray()
            );
    }

    /**
     * Delete token.
     *
     * @param RepositoryReference $repositoryReference
     * @param TokenUUID           $tokenUUID
     *
     * @return PromiseInterface
     */
    public function deleteToken(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    ): PromiseInterface {
        return $this
            ->storage
            ->del(
                $this->composeKey($repositoryReference->getAppUUID()),
                $tokenUUID->composeUUID()
            );
    }

    /**
     * Get tokens.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return PromiseInterface<Token[]>
     */
    public function getTokens(RepositoryReference $repositoryReference): PromiseInterface
    {
        return $this->getTokensByAppUUID($repositoryReference->getAppUUID());
    }

    /**
     * Delete all tokens.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return PromiseInterface
     */
    public function deleteTokens(RepositoryReference $repositoryReference): PromiseInterface
    {
        return $this
            ->storage
            ->delAll($this->composeKey($repositoryReference->getAppUUID()));
    }

    /**
     * Get token by uuid.
     *
     * @param AppUUID   $appUUID
     * @param TokenUUID $tokenUUID
     *
     * @return PromiseInterface<Token|null>
     */
    public function getTokenByUUID(
        AppUUID $appUUID,
        TokenUUID $tokenUUID
    ): PromiseInterface {
        return $this
            ->storage
            ->get($this->composeKey($appUUID), $tokenUUID->composeUUID())
            ->then(function ($token) {

                return is_array($token)
                    ? Token::createFromArray($token)
                    : null;
            });
    }

    /**
     * Get tokens by AppUUID.
     *
     * @param AppUUID $appUUID
     *
     * @return PromiseInterface<Token[]>
     */
    public function getTokensByAppUUID(AppUUID $appUUID): PromiseInterface
    {
        return $this
            ->storage
            ->getAll($this->composeKey($appUUID))
            ->then(function (array $tokens) {
                return array_map(function (array $token) {
                    return Token::createFromArray($token);
                }, $tokens);
            });
    }

    /**
     * Locator is enabled.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->enabled;
    }

    /**
     * Get composed key.
     *
     * @param AppUUID $appUUID
     *
     * @return string
     */
    private function composeKey(AppUUID $appUUID): string
    {
        return 'tokens~~'.$appUUID->composeUUID();
    }
}

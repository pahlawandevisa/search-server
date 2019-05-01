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

use Apisearch\Config\Config;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Exception\TransportableException;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Token\TokenProviders;

/**
 * Class Repository.
 */
final class Repository
{
    /**
     * @var IndexRepository
     *
     * Index Repository
     */
    private $indexRepository;

    /**
     * @var TokenRepository
     *
     * Token repository
     */
    private $tokenRepository;

    /**
     * @var TokenProviders
     *
     * Token providers
     */
    private $tokenProviders;

    /**
     * Repository constructor.
     *
     * @param IndexRepository $indexRepository
     * @param TokenRepository $tokenRepository
     * @param TokenProviders  $tokenProviders
     */
    public function __construct(
        IndexRepository $indexRepository,
        TokenRepository $tokenRepository,
        TokenProviders $tokenProviders
    ) {
        $this->indexRepository = $indexRepository;
        $this->tokenRepository = $tokenRepository;
        $this->tokenProviders = $tokenProviders;
    }

    /**
     * Add token.
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     */
    public function addToken(
        RepositoryReference $repositoryReference,
        Token $token
    ) {
        $this
            ->tokenRepository
            ->addToken(
                $repositoryReference,
                $token
            );
    }

    /**
     * Delete token.
     *
     * @param RepositoryReference $repositoryReference
     * @param TokenUUID           $tokenUUID
     */
    public function deleteToken(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    ) {
        $this
            ->tokenRepository
            ->deleteToken(
                $repositoryReference,
                $tokenUUID
            );
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
        return $this
            ->tokenProviders
            ->getTokensByAppUUID($repositoryReference->getAppUUID());
    }

    /**
     * Delete all tokens.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function deleteTokens(RepositoryReference $repositoryReference)
    {
        $this
            ->tokenRepository
            ->deleteTokens($repositoryReference);
    }

    /**
     * Get indices.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Index[]
     */
    public function getIndices(RepositoryReference $repositoryReference): array
    {
        return $this
            ->indexRepository
            ->getIndices($repositoryReference);
    }

    /**
     * Create an index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     * @param Config              $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID,
        Config $config
    ) {
        $this
            ->indexRepository
            ->createIndex(
                $repositoryReference,
                $indexUUID,
                $config
            );
    }

    /**
     * Delete an index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID
    ) {
        $this
            ->indexRepository
            ->deleteIndex(
                $repositoryReference,
                $indexUUID
            );
    }

    /**
     * Reset the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID
    ) {
        $this
            ->indexRepository
            ->resetIndex(
                $repositoryReference,
                $indexUUID
            );
    }

    /**
     * Checks the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     *
     * @return bool
     */
    public function checkIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID
    ): bool {
        try {
            $indices = $this
                ->indexRepository
                ->getIndices($repositoryReference);

            foreach ($indices as $index) {
                if (
                    $index->getUUID()->composeUUID() == $indexUUID->composeUUID() &&
                    $index->isOK()
                ) {
                    return true;
                }
            }
        } catch (TransportableException $exception) {
            // Silent pass
        }

        return false;
    }

    /**
     * Config the index.
     *
     * @param RepositoryReference $repositoryReference
     * @param IndexUUID           $indexUUID
     * @param Config              $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        RepositoryReference $repositoryReference,
        IndexUUID $indexUUID,
        Config $config
    ) {
        $this
            ->indexRepository
            ->configureIndex(
                $repositoryReference,
                $indexUUID,
                $config
            );
    }
}

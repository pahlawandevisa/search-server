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

namespace Apisearch\Plugin\GenQuery\Domain\Middleware;


use Apisearch\Plugin\GenSearch\Domain\SpeciesManager;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Domain\Query\Query;

/**
 * Class QueryMiddleware.
 */
class QueryMiddleware implements PluginMiddleware
{
    /**
     * @var SpeciesManager
     *
     * Species Manager
     */
    private $speciesManager;

    /**
     * QueryMiddleware constructor.
     *
     * @param SpeciesManager $speciesManager
     */
    public function __construct(SpeciesManager $speciesManager)
    {
        $this->speciesManager = $speciesManager;
    }

    /**
     * Execute middleware.
     *
     * @param mixed    $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute(
        $command,
        $next
    ) {
        $query = $command->getQuery();
        $newQuery = $this
            ->speciesManager
            ->applyGeneticChange($query);

        return $next(new Query(
            $command->getRepositoryReference(),
            $command->getToken(),
            $newQuery
        ));
    }

    /**
     * Events subscribed namespace. Can refer to specific class namespace, any
     * parent class or any interface.
     *
     * By returning an empty array, means coupled to all.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Query::class];
    }
}

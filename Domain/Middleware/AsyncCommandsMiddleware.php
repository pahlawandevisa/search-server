<?php


namespace Apisearch\Server\Domain\Middleware;


use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandEnqueuer\CommandEnqueuer;
use League\Tactician\Middleware;

/**
 * Class AsyncCommandsMiddleware
 */
final class AsyncCommandsMiddleware implements Middleware
{
    /**
     * @var CommandEnqueuer
     *
     * Command enqueuer
     */
    private $commandEnqueuer;

    /**
     * AsynchronousCommandIngestor constructor.
     *
     * @param CommandEnqueuer $commandEnqueuer
     */
    public function __construct(CommandEnqueuer $commandEnqueuer)
    {
        $this->commandEnqueuer = $commandEnqueuer;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof AsynchronousableCommand) {
            return $this
                ->commandEnqueuer
                ->enqueueCommand($command);
        }

        return $next($command);
    }
}
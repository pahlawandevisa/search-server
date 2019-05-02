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

namespace Apisearch\Server\Domain\CommandBus;

use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandEnqueuer\CommandEnqueuer;
use Clue\React\Block;
use League\Tactician\CommandBus;
use React\EventLoop\LoopInterface;

/**
 * Class AsynchronousCommandBus.
 */
class AsynchronousCommandBus extends CommandBus
{
    /**
     * @var CommandEnqueuer
     *
     * Command enqueuer
     */
    private $commandEnqueuer;

    /**
     * @var CommandBus
     *
     * Command bus
     */
    private $commandBus;

    /**
     * @var LoopInterface
     *
     * Loop interface
     */
    private $loop;

    /**
     * AsynchronousCommandIngestor constructor.
     *
     * @param CommandEnqueuer $commandEnqueuer
     * @param CommandBus      $commandBus
     * @param LoopInterface   $loop
     */
    public function __construct(
        CommandEnqueuer $commandEnqueuer,
        CommandBus $commandBus,
        LoopInterface $loop
    ) {
        parent::__construct([]);
        $this->commandEnqueuer = $commandEnqueuer;
        $this->commandBus = $commandBus;
        $this->loop = $loop;
    }

    /**
     * Executes the given command and optionally returns a value.
     *
     * @param object $command
     *
     * @return mixed
     */
    public function handle($command)
    {
        return Block\await(
            $command instanceof AsynchronousableCommand
                ? $this
                    ->commandEnqueuer
                    ->enqueueCommand($command)
                : $this
                    ->commandBus
                    ->handle($command),
            $this->loop
        );
    }
}

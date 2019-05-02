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

use Clue\React\Block;
use League\Tactician\CommandBus;
use React\EventLoop\LoopInterface;

/**
 * Class InlineCommandBus.
 */
class InlineCommandBus extends CommandBus
{
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
     * AwaitCommandBus constructor.
     *
     * @param CommandBus    $commandBus
     * @param LoopInterface $loop
     */
    public function __construct(
        CommandBus $commandBus,
        LoopInterface $loop
    ) {
        parent::__construct([]);
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
            $this
                ->commandBus
                ->handle($command),
            $this->loop
        );
    }
}

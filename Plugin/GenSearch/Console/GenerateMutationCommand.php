<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Console;

use Apisearch\Plugin\GenSearch\Domain\Command\GenerateMutation;
use Apisearch\Server\Console\CommandWithBusAndGodToken;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateMutationCommand
 */
class GenerateMutationCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setDescription('Genetate mutation');
    }

    /**
     * Dispatch domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed|null
     */
    protected function runCommand(InputInterface $input, OutputInterface $output)
    {
        $this
            ->commandBus
            ->handle(new GenerateMutation());

        return;
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected static function getHeader(): string
    {
        return 'Generate mutation';
    }

    /**
     * Get success message.
     *
     * @param InputInterface $input
     * @param mixed          $result
     *
     * @return string
     */
    protected static function getSuccessMessage(
        InputInterface $input,
        $result
    ): string {
        return sprintf('Mutation generated properly', $result);
    }
}
<?php
/*
 * This file is part of the {Package name}.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Apisearch\Server\Console;

use Apisearch\Server\Domain\Model\CrontabLine;
use Apisearch\Server\Domain\Query\GetCrontab;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCrontabCommand
 */
class GenerateCrontabCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setDescription('Create crontab file');
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
        $lines = $this
            ->commandBus
            ->handle(new GetCrontab());

        $lines = array_map(function(CrontabLine $line) {
            return $line->toString(realpath(__DIR__ . '/../'));
        }, $lines);

        file_put_contents('/tmp/crontab.txt', implode(PHP_EOL, $lines));
        exec('crontab /tmp/crontab.txt');

        return;
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected static function getHeader(): string
    {
        return 'Generate crontab';
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
        return sprintf('Crontab generated properly', $result);
    }
}
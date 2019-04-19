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

namespace Apisearch\Server\Domain\QueryHandler;

use Apisearch\Server\Domain\Model\CrontabLine;
use Apisearch\Server\Domain\Query\GetCrontab;

/**
 * Class GetCrontabHandler.
 */
class GetCrontabHandler
{
    /**
     * Get the crontab.
     *
     * @param GetCrontab $getCrontab
     *
     * @return CrontabLine[]
     */
    public function handle(GetCrontab $getCrontab): array
    {
        return $getCrontab->getLines();
    }
}

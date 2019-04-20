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

namespace Apisearch\Server\Tests\Functional\Console\Crontab;

use Apisearch\Server\Domain\Model\CrontabLine;
use Apisearch\Server\Domain\Plugin\CrontabMiddleware;

/**
 * Class FakeCrontabMiddleware.
 */
class FakeCrontabMiddleware extends CrontabMiddleware
{
    /**
     * Get crontabs.
     *
     * @return CrontabLine[]
     */
    protected function getCrontabLines(): array
    {
        return [
            new CrontabLine('1', '*', '*', '*', '*', 'blah1.sh'),
        ];
    }
}

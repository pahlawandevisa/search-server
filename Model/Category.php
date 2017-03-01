<?php

/*
 * This file is part of the SearchBundle for Symfony2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\SearchBundle\Model;

/**
 * Class Category.
 */
class Category extends IdNameWrapper implements WithLevel
{
    /**
     * @var string
     *
     * Name
     */
    const TYPE = 'category';

    /**
     * @var int
     *
     * Level
     */
    private $level;

    /**
     * Category constructor.
     *
     * @param string $id
     * @param string $name
     * @param int    $level
     */
    public function __construct(
        string $id,
        string $name,
        int $level = 1
    ) {
        parent::__construct($id, $name);

        $this->level = $level;
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return static
     */
    public static function createFromArray(array $array)
    {
        if (
            !isset($array['id']) ||
            !isset($array['name'])
        ) {
            return null;
        }

        return new static(
            (string) $array['id'],
            (string) $array['name'],
            $array['level'] ?? 1
        );
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}

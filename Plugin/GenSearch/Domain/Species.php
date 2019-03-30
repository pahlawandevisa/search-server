<?php
/**
 * File header placeholder
 */

namespace Apisearch\Plugin\GenSearch\Domain;

use Apisearch\Query\Query;
use DateTime;

/**
 * Class Species
 */
class Species
{
    /**
     * @var string
     *
     * Species UUID
     */
    private $UUID;

    /**
     * @var string
     *
     * Parent UUID
     */
    private $parentUUID;

    /**
     * @var Query
     *
     * Query
     */
    private $query;

    /**
     * @var DateTime
     *
     * Species creation
     */
    private $createdAt;

    /**
     * @var int[]
     *
     * Events
     */
    private $events;

    /**
     * Get Uuid
     *
     * @return string
     */
    public function getUUID() : string
    {
        return $this->UUID;
    }

    /**
     * Get Query
     *
     * @return Query
     */
    public function getQuery() : Query
    {
        return $this->query;
    }

    /**
     * Get CreatedAt
     *
     * @return DateTime
     */
    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get Events
     *
     * @return int[]
     */
    public function getEvents() : array
    {
        return $this->events;
    }
}
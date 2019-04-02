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

namespace Apisearch\Plugin\GenSearch\Domain;

use Apisearch\Server\Domain\Event\InteractionWasAdded;

/**
 * Class AddClickInteraction
 */
class AddClickInteraction
{
    /**
     * @var int
     *
     * Max position
     */
    private $maxPosition;

    /**
     * @var SpeciesRepository
     *
     * Species Repository
     */
    private $speciesRepository;

    /**
     * OnInteractionAdded constructor.
     *
     * @param int $maxPosition
     * @param SpeciesRepository $speciesRepository
     */
    public function __construct(
        int $maxPosition,
        SpeciesRepository $speciesRepository
    )
    {
        $this->maxPosition = $maxPosition;
        $this->speciesRepository = $speciesRepository;
    }

    /**
     * Add interaction
     *
     * @param InteractionWasAdded $interactionWasAdded
     *
     * @return void
     */
    public function addClick(InteractionWasAdded $interactionWasAdded) : void
    {
        $interaction = $interactionWasAdded->getInteraction();
        $metadata = $interaction->getMetadata();
        if (
            $interaction->getEventName() !== 'search_result_click' ||
            !isset($metadata['query_uuid']) ||
            empty($metadata['query_uuid']) ||
            !isset($metadata['position']) ||
            $metadata['position'] <= 0
        ) {
            return;
        }

        $queryUUID = $metadata['query_uuid'];
        $position = $metadata['position'];
        $position = max($position, $this->maxPosition);
        $eventName = 'click_position_' . $position;

        $this
            ->speciesRepository
            ->increaseQueryEvent(
                $queryUUID,
                $eventName
            );
    }
}
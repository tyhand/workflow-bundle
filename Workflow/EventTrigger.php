<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException;

/**
 * Container for the event trigger
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class EventTrigger
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Name of the event that will trigger the transition
     *
     * @var string
     */
    private $eventName;

    /**
     * Name of the followup state
     *
     * @var string
     */
    private $stateName;

    /**
     * The followup state
     *
     * @var State
     */
    private $state;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string $eventName Name of the event to trigger the transition
     * @param string $stateName Name of the state to transition to
     */
    public function __construct($eventName, $stateName)
    {
        // Set
        $this->eventName = $eventName;
        $this->stateName = $stateName;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Check if the full followup state has been set
     *
     * @return boolean Whether the followup state has been set
     */
    public function isComplete()
    {
        return (null !== $this->state);
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Name of the event that will trigger the transition
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Get the value of Name of the followup state
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Get the value of The followup state
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of The followup state
     *
     * @param State state
     *
     * @return self
     */
    public function setState(State $state)
    {
        if ($state->getName() === $this->stateName) {
            $this->state = $state;
        } else {
            throw new StateDoesNotMatchNameException($state, $this->stateName);
        }

        return $this;
    }

}

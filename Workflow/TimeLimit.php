<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException;

/**
 * Simple class for holding the time limit trigger information
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class TimeLimit
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Time limit amount in seconds
     *
     * @var int
     */
    private $timeLimit;

    /**
     * Name of the state to go to if the time limit is passed
     *
     * @var string
     */
    private $stateName;

    /**
     * Followup state if the time limit is passed
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
     * @param int    $timeLimit Time limit in seconds
     * @param string $stateName Name of the followup state if limit is passed
     */
    public function __construct($timeLimit, $stateName)
    {
        // Set
        $this->timeLimit = $timeLimit;
        $this->stateName = $stateName;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Checks if the time limit object is complete (aka the state has been set)
     *
     * @return boolean True if complete
     */
    public function isComplete()
    {
        // Just check that the state has been set
        return ($this->state !== null);
    }

    /**
     * Check if the time limit is up
     *
     * @param  DateTime $started Start time
     *
     * @return boolean True if the time limit is up
     */
    public function isPassed(\DateTime $started)
    {
        // Create a new datetime to now, subtract the limit from it and see if its less than started
        $now = new \DateTime();
        $now->sub(\DateInterval::createFromDateString(sprintf('%d seconds', $this->timeLimit)));
        return ($now >= $started);
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Time limit amount in seconds
     *
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * Get the value of Name of the state to go to if the time limit is passed
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Get the value of Followup state if the time limit is passed
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of Followup state if the time limit is passed
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

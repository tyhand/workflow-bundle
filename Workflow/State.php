<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Workflow\Context\ContextInterface;
use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

/**
 * Workflow state
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class State
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Name of the state
     *
     * @var string
     */
    private $name;

    /**
     * List of callbacks to be called when a state enters a function
     *
     * @var array
     */
    private $actions;

    /**
     * List of condition callbacks to be evaluated when a context enters the state
     *
     * @var array
     */
    private $conditions;

    /**
     * List of the event triggers for this state
     *
     * @var array
     */
    private $events;

    /**
     * Time limit for this state if one exists
     *
     * @var TimeLimit
     */
    private $timeLimit;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string $name Name of the state
     */
    public function __construct($name)
    {
        // Set
        $this->name = $name;

        // Init
        $this->actions = array();
        $this->conditions = array();
        $this->events = array();
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Move into this state
     *
     * @param  ContextInterface       $context        Workflow context
     * @param  WorkflowInstanceEntity $instanceEntity Instance entity
     *
     * @return WorkflowInstanceEntity Updated instance entity
     */
    public function moveTo(ContextInterface $context, WorkflowInstanceEntity $instanceEntity)
    {
        // Update the instance entity
        $instanceEntity->setStateName($this->getName());
        $instanceEntity->setStateDate(new \DateTime());

        // Call the actions
        $this->callActions($context);

        // Handle the conditions
        $next = $this->evaluateConditions($context);
        if ($next !== null) {
            return $next->moveTo($context, $instanceEntity);
        }

        // Check if this is a terminal state
        if ($this->isTerminal()) {
            $instanceEntity->setIsComplete(true);
        }

        // Return the updated instance entity
        return $instanceEntity;
    }

    /**
     * Add an action callback to the state
     *
     * @param callable $action Anonymous action function
     *
     * @return self
     */
    public function addAction(callable $action)
    {
        // Add to the actions array
        $this->actions[] = $action;
        return $this;
    }

    /**
     * Add a condition to the state
     *
     * @param Condition $condition Condition to add
     *
     * @return self
     */
    public function addCondition(Condition $condition)
    {
        // Add to the conditions array
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Add an event trigger to the state
     *
     * @param EventTrigger $eventTrigger Event trigger to add
     *
     * @return self
     */
    public function addEventTrigger(EventTrigger $eventTrigger)
    {
        // Add to the event triggers array
        $this->events[$eventTrigger->getEventName()] = $eventTrigger;
        return $this;
    }

    /**
     * Call each action with the context
     *
     * @param  mixed  $context Context
     *
     * @return self
     */
    public function callActions($context)
    {
        // Call each action with the given context
        foreach($this->actions as $action) {
            call_user_func($action, $context, $this);
        }
        return $this;
    }

    /**
     * Go through the conditions and see if any move to a new state
     *
     * @param  mixed      $context Context
     *
     * @return State|null Followup state if one is appropiate
     */
    public function evaluateConditions($context)
    {
        // Evaluate each condition and return the first followup state
        // if no followup states, return null
        foreach($this->conditions as $condition) {
            $return = $condition->evaluate($context);
            if (null !== $return) {
                return $return;
            }
        }

        return null;
    }

    /**
     * Check if this state has a time limit
     *
     * @return boolean Does the the state have the time limit
     */
    public function hasTimeLimit()
    {
        return (null !== $this->timeLimit);
    }

    /**
     * Check if the time limit has passed if one exists
     * NOTE: this method will return null if the time limit is not set
     *
     * @param  DateTime $started When the context was placed in the state
     *
     * @return State|null Time limit followup state if the limit is passed, null elsewise
     */
    public function hasTimeLimitPassed(\DateTime $started)
    {
        if ($this->hasTimeLimit()) {
            if ($this->timeLimit->isPassed($started)) {
                return $this->timeLimit->getState();
            } else {
                return null;
            }
        } else {
            return null; // Cant be passed a non-existent time limit
        }
    }

    /**
     * Check if this state is a terminal state
     * NOTE: Currently this method needs to be checked AFTER conditions are called
     *
     * @return boolean Whether the state is a terminal state
     */
    public function isTerminal()
    {
        // If there is no time limit, events, or conditions
        if (!$this->hasTimeLimit() && 0 === count($this->events)) {
            return true;
        }

        // Nope
        return false;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the name of the state
     *
     * @return string State name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the array of actions
     *
     * @return array Array of callables
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the array of events (eventtriggers keyed by event name)
     *
     * @return array Event trigger array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Get the time limit if set
     *
     * @return TimeLimit Time limit
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * Set a time limit for this state
     *
     * @param TimeLimit $timeLimit Time limit
     *
     * @return self
     */
    public function setTimeLimit(TimeLimit $timeLimit)
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }
}

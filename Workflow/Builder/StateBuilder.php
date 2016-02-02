<?php

namespace TyHand\WorkflowBundle\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\State;
use TyHand\WorkflowBundle\Workflow\Condition;
use TyHand\WorkflowBundle\Workflow\TimeLimit;
use TyHand\WorkflowBundle\Workflow\EventTrigger;
use TyHand\WorkflowBundle\Exceptions\StateNotFoundException;

/**
 * Builder for a workflow state
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class StateBuilder
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Under constructions state
     *
     * @var State
     */
    private $state;

    /**
     * Parent workflow builder
     *
     * @var WorkflowBuilder
     */
    private $parent;

    /**
     * Array of conditions builders that are children of this builder
     *
     * @var array
     */
    private $conditionBuilders;

    /**
     * Array of event triggers
     *
     * @var array
     */
    private $eventTriggers;

    /**
     * Time limit if one exists
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
     * @param WorkflowBuilder $parent    Parent Workflowbuilder
     * @param string          $stateName Name of the new state
     */
    public function __construct(WorkflowBuilder $parent, $stateName)
    {
        // Set
        $this->parent = $parent;

        // State the new state
        $this->state = new State($stateName);

        // init
        $this->conditionBuilders = array();
        $this->eventTriggers = array();
        $this->timeLimit = null;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Build the state
     *
     * @param array Map of states keyed by name for the workflow
     *
     * @return State Newly minted state
     */
    public function build(array $stateMap)
    {
        // Build each condition trigger
        foreach($this->conditionBuilders as $conditionBuilder) {
            $this->state->addCondition($conditionBuilder->build($stateMap));
        }

        // Build each event trigger
        foreach($this->eventTriggers as $eventTrigger) {
            if (array_key_exists($eventTrigger->getStateName(), $stateMap)) {
                $eventTrigger->setState($stateMap[$eventTrigger->getStateName()]);
                $this->state->addEventTrigger($eventTrigger);
            } else {
                throw new StateNotFoundException($eventTrigger->getStateName(), array_keys($stateMap));
            }
        }

        // Build each time limit trigger
        if (null !== $this->timeLimit) {
            if (array_key_exists($this->timeLimit->getStateName(), $stateMap)) {
                $this->timeLimit->setState($stateMap[$this->timeLimit->getStateName()]);
                $this->state->setTimeLimit($this->timeLimit);
            } else {
                throw new StateNotFoundException($this->timeLimit->getStateName(), array_keys($stateMap));
            }
        }

        // Return the complete state
        return $this->state;
    }

    /**
     * Add a function to be called when the context enters the state
     * function footprint as follows (function ($context, $state))
     *
     * @param callable $action Anonymous callback
     *
     * @return self
     */
    public function addAction(callable $action)
    {
        // Add to the state
        $this->state->addAction($action);
        return $this;
    }

    /**
     * Add an event trigger
     *
     * @param string $eventName         Name of the triggering event
     * @param string $followupStateName Name of the followup state
     *
     * @return self
     */
    public function addEvent($eventName, $followupStateName)
    {
        $this->eventTriggers[] = new EventTrigger($eventName, $followupStateName);
        return $this;
    }

    /**
     * Set a time limit for this state
     *
     * @param int    $limit             Time limit in seconds
     * @param string $followupStateName Name of the state to go to if the limit expires
     *
     * @return self
     */
    public function setTimeLimit($limit, $followupStateName)
    {
        $this->timeLimit = new TimeLimit($limit, $followupStateName);
        return $this;
    }

    /**
     * Start a new condition
     *
     * @return ConditionBuilder New condition builder
     */
    public function startCondition()
    {
        $conditionBuilder = new ConditionBuilder($this);
        $this->conditionBuilders[] = $conditionBuilder;
        return $conditionBuilder;
    }

    /**
     * End the state configuration
     *
     * @return WorkflowBuilder Parent builder
     */
    public function end()
    {
        return $this->parent;
    }

    /**
     * Get the name of the state being built
     *
     * @return string State name
     */
    public function getStateName()
    {
        return $this->state->getName();
    }

    /**
     * Get the unbuilt state
     * used by the workflow builder to generate the statemap
     *
     * @return State Unfinished state
     */
    public function getUnbuiltState()
    {
        return $this->state;
    }
}

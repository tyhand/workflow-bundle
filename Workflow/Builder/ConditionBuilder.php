<?php

namespace TyHand\WorkflowBundle\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\Condition;
use TyHand\WorkflowBundle\Exceptions\StateNotFoundException;

class ConditionBuilder
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Parent State Builder
     *
     * @var StateBuilder
     */
    private $parent;

    /**
     * Condition callback
     *
     * @var callable
     */
    private $condition;

    /**
     * Name of the followup state if evaluated to true, if one exists
     *
     * @var string
     */
    private $trueStateName;

    /**
     * Name of the followup state if evaluated to false, if one exists
     *
     * @var string
     */
    private $falseStateName;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param StateBuilder $parent Parent state builder
     */
    public function __construct(StateBuilder $parent)
    {
        // Set
        $this->parent = $parent;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Finalize the condition construction and return to the parent builder
     *
     * @return StateBuilder Parent state builder
     */
    public function end()
    {
        // Return the parent builder
        return $this->parent;
    }

    /**
     * Build the condition
     *
     * @param array Map of the states in the workflow keyed by name
     *
     * @return Condition Newly built condition
     */
    public function build(array $stateMap)
    {
        // Create the condition object
        $condition = new Condition($this->condition, $this->trueStateName, $this->falseStateName);

        // Get the states from the state map if necessary
        if (null !== $this->trueStateName) {
            if (array_key_exists($this->trueStateName, $stateMap)) {
                $condition->setTrueState($stateMap[$this->trueStateName]);
            } else {
                throw new StateNotFoundException($this->trueStateName, array_keys($stateMap));
            }
        }
        if (null !== $this->falseStateName) {
            if (array_key_exists($this->falseStateName, $stateMap)) {
                $condition->setFalseState($stateMap[$this->falseStateName]);
            } else {
                throw new StateNotFoundException($this->falseStateName, array_keys($stateMap));
            }
        }

        // return
        return $condition;
    }

    /**
     * Add the condition callback function
     * Should have an footprint as follows
     * function($context) { ... }
     * and MUST return true or false
     *
     * @param  callable $condition Condition callback method
     *
     * @return self
     */
    public function conditionFunction(callable $condition)
    {
        // Set
        $this->condition = $condition;
        return $this;
    }

    /**
     * Name of the state to go to if the condition is evaluated as true
     *
     * @param  string $trueStateName Name of the followup state
     *
     * @return self
     */
    public function ifTrue($trueStateName)
    {
        // Set
        $this->trueStateName = $trueStateName;
        return $this;
    }

    /**
     * Name of the state to go to if the condition is evaluated as false
     *
     * @param  stirng $falseStateName Name of the followup state
     *
     * @return self
     */
    public function ifFalse($falseStateName)
    {
        // Set
        $this->falseStateName = $falseStateName;
        return $this;
    }
}

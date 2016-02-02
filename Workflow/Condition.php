<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException;

/**
 * Holds information for a state condition
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class Condition
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Callback function that returns either true or false
     *
     * @var callable
     */
    private $condition;

    /**
     * Name of the state to move to if the state is false
     *
     * @var string
     */
    private $trueStateName;

    /**
     * Name of the state to move to if the condition is false
     *
     * @var string
     */
    private $falseStateName;

    /**
     * State to move to if the condition is true
     *
     * @var State
     */
    private $trueState;

    /**
     * State to move to if the condition is false
     *
     * @var State
     */
    private $falseState;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * Since the name of the states are available before the states may be built
     * this constructor will only take the name of the states and assumes that
     * later the built states will be added in
     *
     * @param callable $condition      Condition
     * @param string   $trueStateName  Name of followup true state if exists
     * @param string   $falseStateName Name of followup false state if exists
     */
    public function __construct(callable $condition, $trueStateName = null, $falseStateName = null)
    {
        // Set
        $this->condition = $condition;
        $this->trueStateName = $trueStateName;
        $this->falseStateName = $falseStateName;
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Checks that each state name has a matching state
     *
     * @return boolean True if the condition appears to be complete
     */
    public function isComplete()
    {
        // Check that for each set state name, a state was set also
        if (null !== $this->trueStateName && null === $this->trueState) {
            return false;
        } elseif (null !== $this->falseStateName && null === $this->falseState) {
            return false;
        }

        // If everything passed return true
        return true;
    }

    /**
     * Evaluate
     * Takes in the context to evaluate, runs through the condition and
     * returns the appropiate state if it exists.  If, for example, no followup
     * state is set for if the condition evaluates false, and the condition evals
     * to false, then null will be returned
     *
     * @param  mixed      $context Context to evaluate with
     *
     * @return State|null          Followup state if one exists
     */
    public function evaluate($context)
    {
        $evaluation = call_user_func($this->condition, $context);
        if (true === $evaluation && null !== $this->trueState) {
            return $this->trueState;
        } elseif (false === $evaluation && null !== $this->falseState) {
            return $this->falseState;
        }
        return null;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Callback function that returns either true or false
     *
     * @return callable
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Get the value of Name of the state to move to if the state is false
     *
     * @return string
     */
    public function getTrueStateName()
    {
        return $this->trueStateName;
    }

    /**
     * Get the value of Name of the state to move to if the condition is false
     *
     * @return string
     */
    public function getFalseStateName()
    {
        return $this->falseStateName;
    }

    /**
     * Get the value of State to move to if the condition is true
     *
     * @return State
     */
    public function getTrueState()
    {
        return $this->trueState;
    }

    /**
     * Set the value of State to move to if the condition is true
     *
     * @param State trueState
     *
     * @return self
     */
    public function setTrueState(State $trueState)
    {
        if ($trueState->getName() === $this->trueStateName) {
            $this->trueState = $trueState;
        } else {
            throw new StateDoesNotMatchNameException($trueState, $this->trueStateName);
        }

        return $this;
    }

    /**
     * Get the value of State to move to if the condition is false
     *
     * @return State
     */
    public function getFalseState()
    {
        return $this->falseState;
    }

    /**
     * Set the value of State to move to if the condition is false
     *
     * @param State falseState
     *
     * @return self
     */
    public function setFalseState(State $falseState)
    {
        if ($falseState->getName() === $this->falseStateName) {
            $this->falseState = $falseState;
        } else {
            throw new StateDoesNotMatchNameException($falseState, $this->falseStateName);
        }

        return $this;
    }
}

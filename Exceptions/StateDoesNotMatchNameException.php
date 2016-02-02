<?php

namespace TyHand\WorkflowBundle\Exceptions;

use TyHand\WorkflowBundle\Workflow\State;

/**
 * Exception thrown when a state is added in a place where the states name was
 * already in place and the added state does not match the name
 */
class StateDoesNotMatchNameException extends RuntimeException
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * State that caused the exception
     *
     * @var State
     */
    private $state;

    /**
     * Name of the state the object was expecting
     *
     * @var array
     */
    private $setName;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param State      $state    State the caused the exception
     * @param string     $setName  Name of the state the object was expecting
     * @param \Exception $previous Optional previous exception
     */
    public function __construct(State $state, $setName, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'Trying to set state with name "%s" when object was expecting a state with name "%s"',
                $state->getName(),
                $setName
            ),
            0,
            $previous
        );

        // Set the properties
        $this->state = $state;
        $this->setName = $setName;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of State that caused the exception
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the value of Name of the state the object was expecting
     *
     * @return array
     */
    public function getSetName()
    {
        return $this->setName;
    }
}

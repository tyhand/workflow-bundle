<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Exceptions\StateNameAlreadyUsedException;
use TyHand\WorkflowBundle\Exceptions\StateNotFoundException;
use TyHand\WorkflowBundle\Exceptions\ContextOverWorkflowLimitException;
use TyHand\WorkflowBundle\Exceptions\ContextNotAcceptedByWorkflowException;
use TyHand\WorkflowBundle\Workflow\Context\ContextInterface;
use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

/**
 * Workflow
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class Workflow
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Name of the workflow
     *
     * @var string
     */
    private $name;

    /**
     * Map of states in the workflow keyed by unique name
     *
     * @var array
     */
    private $states;

    /**
     * Class of the context object
     *
     * @var string
     */
    private $contextClass;

    /**
     * Starting state in the workflow
     *
     * @var State
     */
    private $initialState;

    /**
     * Optional limit on the number of active instances a context can have
     *
     * @var int
     */
    private $activeInstanceLimit;

    /**
     * Optional limit on the number of total instances a context can have
     *
     * @var int
     */
    private $totalInstanceLimit;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string $name Name of the workflow
     */
    public function __construct($name, $contextClass)
    {
        // Set
        $this->name = $name;
        $this->contextClass = $contextClass;

        // Init
        $this->states = array();
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Put a context into the workflow
     *
     * @return WorkflowInstanceEntity New instance entity
     */
    public function start(ContextInterface $context)
    {
        // Check that the context can be placed into the workflow
        if (!$this->checkType($context)) {
            throw new ContextNotAcceptedByWorkflowException(get_class($context), $this->contextClass);
        }

        // Check that the context is within the limits of the workflow
        if ($context->hasWorkflowInstancesForWorkflow($this->getName())) {
            if (null !== $this->activeInstanceLimit) {
                if ($this->activeInstanceLimit <= count($context->getWorkflowInstancesForWorkflow($this->getName(), true))) {
                    throw new ContextOverWorkflowLimitException(
                        $this->activeInstanceLimit,
                        ContextOverWorkflowLimitException::ACTIVE_LIMIT
                    );
                }
            }
            if (null !== $this->totalInstanceLimit) {
                if ($this->totalInstanceLimit <= count($context->getWorkflowInstancesForWorkflow($this->getName(), false))) {
                    throw new ContextOverWorkflowLimitException(
                        $this->totalInstanceLimit,
                        ContextOverWorkflowLimitException::TOTAL_LIMIT
                    );
                }
            }
        }

        // Create the new workflow instance entity and move to the initial state
        $instanceEntity = new WorkflowInstanceEntity();
        $instanceEntity->setWorkflowName($this->getName());

        // Add the instance entity to the context
        $context->addWorkflowInstance($instanceEntity);

        // Move to the first state
        return $this->getInitialState()->moveTo($context, $instanceEntity);
    }

    /**
     * Check that a given context is of the correct class
     *
     * @param  mixed  $context Context object
     *
     * @return boolean True if type is correct, false elsewise
     */
    public function checkType($context)
    {
        return ($context instanceof $this->contextClass);
    }

    /**
     * Add a state to the workflow
     *
     * @param State $state State
     *
     * @return self
     */
    public function addState(State $state)
    {
        // Check for name uniqueness
        if (array_key_exists($state->getName(), $this->states)) {
            throw new StateNameAlreadyUsedException($state->getName());
        }
        $this->states[$state->getName()] = $state;

        return $this;
    }

    /**
     * Get a state from the workflow by name
     *
     * @param  string $stateName Name of the state to get
     *
     * @return State State
     */
    public function getState($stateName)
    {
        if (array_key_exists($stateName, $this->states)) {
            return $this->states[$stateName];
        } else {
            throw new StateNotFoundException($stateName, array_keys($this->states));
        }
    }

    /**
     * Get an array of all time limited states in the workflow
     *
     * @return array Array of states with time limits
     */
    public function getTimeLimitedStates()
    {
        $timeLimitedStates = array();
        foreach($this->states as $state) {
            if ($state->hasTimeLimit()) {
                $timeLimitedStates[] = $state;
            }
        }
        return $timeLimitedStates;
    }

    /**
     * Get the count of the states
     *
     * @return int Number of states in the workflow
     */
    public function getNumberOfStates()
    {
        return count($this->states);
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Name of the workflow
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of Class of the context object
     *
     * @return string
     */
    public function getContextClass()
    {
        return $this->contextClass;
    }

    /**
     * Get a list of all the state names in this workflow
     *
     * @return array List of names
     */
    public function getStateNames()
    {
        return array_keys($this->states);
    }

    /**
     * Get the initial state
     *
     * @return State Initial state
     */
    public function getInitialState()
    {
        return $this->initialState;
    }

    /**
     * Set the initial state
     *
     * @param State $initialState Initial state
     */
    public function setInitialState(State $initialState)
    {
        $this->initialState = $initialState;
        return $this;
    }

    /**
     * Get the value of Optional limit on the number of active instances a context can have
     *
     * @return int
     */
    public function getActiveInstanceLimit()
    {
        return $this->activeInstanceLimit;
    }

    /**
     * Set the value of Optional limit on the number of active instances a context can have
     *
     * @param int activeInstanceLimit
     *
     * @return self
     */
    public function setActiveInstanceLimit($activeInstanceLimit)
    {
        $this->activeInstanceLimit = $activeInstanceLimit;
        return $this;
    }

    /**
     * Get the value of Optional limit on the number of total instances a context can have
     *
     * @return int
     */
    public function getTotalInstanceLimit()
    {
        return $this->totalInstanceLimit;
    }

    /**
     * Set the value of Optional limit on the number of total instances a context can have
     *
     * @param int totalInstanceLimit
     *
     * @return self
     */
    public function setTotalInstanceLimit($totalInstanceLimit)
    {
        $this->totalInstanceLimit = $totalInstanceLimit;
        return $this;
    }

}

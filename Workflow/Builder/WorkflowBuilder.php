<?php

namespace TyHand\WorkflowBundle\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\Workflow;
use TyHand\WorkflowBundle\Exceptions\StateNameAlreadyUsedException;
use TyHand\WorkflowBundle\Exceptions\StateNotFoundException;
use TyHand\WorkflowBundle\Exceptions\ContextDoesNotImplementInterfaceException;

/**
 * Builder for the workflow
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class WorkflowBuilder
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Child state builders
     *
     * @var StateBuilder
     */
    private $stateBuilders;

    /**
     * Name of the Initial state
     *
     * @var string
     */
    private $initialStateName;

    /**
     * Class of the context
     *
     * @var string
     */
    private $contextClass;

    /**
     * Name of the workflow
     *
     * @var string
     */
    private $workflowName;

    /**
     * Holder for the active limit
     *
     * @var int
     */
    private $activeLimit;

    /**
     * Holder for the total limit
     *
     * @var int
     */
    private $totalLimit;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string $workflowName Name of the workflow
     */
    public function __construct($workflowName)
    {
        // Set
        $this->workflowName = $workflowName;

        // Init the builder
        $this->stateBuilders = array();
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Finalize the workflow
     *
     * @return Workflow Constructed workflow
     */
    public function build()
    {
        // Build the workflow
        $workflow = new Workflow($this->workflowName, $this->contextClass);

        // Set the limits if they exist
        if ($this->activeLimit !== null) {
            $workflow->setActiveInstanceLimit($this->activeLimit);
        }

        if ($this->totalLimit !== null) {
            $workflow->setTotalInstanceLimit($this->totalLimit);
        }

        // Construct the map of states
        $stateMap = array();
        foreach($this->stateBuilders as $stateName => $stateBuilder) {
            $stateMap[$stateName] = $stateBuilder->getUnbuiltState();
        }

        // Build each state
        foreach($this->stateBuilders as $stateBuilder) {
            $workflow->addState($stateBuilder->build($stateMap));
        }

        // Set the Initial state
        if (!array_key_exists($this->initialStateName, $stateMap)) {
            throw new StateNotFoundException($this->initialStateName, array_keys($stateMap));
        }
        $workflow->setInitialState($stateMap[$this->initialStateName]);

        // Return the completed workflow
        return $workflow;
    }

    /**
     * Set the name of the initial starting state
     *
     * @param  string $initialStateName Name of the state to set as the Initial state
     *
     * @return self
     */
    public function initial($initialStateName)
    {
        $this->initialStateName = $initialStateName;
        return $this;
    }

    /**
     * Class of the context
     *
     * @param  string $contextClass Name of the context class
     *
     * @return self
     */
    public function contextClass($contextClass)
    {
        // Check that the context class uses the context interface
        if (!in_array('TyHand\WorkflowBundle\Workflow\Context\ContextInterface', class_implements($contextClass))) {
            throw new ContextDoesNotImplementInterfaceException($contextClass, 'TyHand\WorkflowBundle\Workflow\Context\ContextInterface');
        }
        $this->contextClass = $contextClass;
        return $this;
    }

    /**
     * Set the number of times a context can be active in a workflow
     *
     * @param  int $activeLimit Limit on how many active instances a context can have
     *
     * @return self
     */
    public function activeLimit($activeLimit)
    {
        $this->activeLimit = $activeLimit;
        return $this;
    }

    /**
     * Set the number of times can be in workflow, complete or not
     *
     * @param  int $totalLimit Limit on the total number of instances a context can have
     *
     * @return self
     */
    public function totalLimit($totalLimit)
    {
        $this->totalLimit = $totalLimit;
        return $this;
    }

    /**
     * Start the configuration for a new state
     *
     * @param  string $stateName Name of the new state
     *
     * @return StateBuilder Builder for the new state
     */
    public function startState($stateName)
    {
        // Check that the state name is not already in this workflow
        if (array_key_exists($stateName, $this->stateBuilders)) {
            throw new StateNameAlreadyUsedException($stateName);
        }

        // Create a new state builder
        $this->stateBuilders[$stateName] = new StateBuilder($this, $stateName);

        // Return the state builder for config chaining
        return $this->stateBuilders[$stateName];
    }

    /**
     * Get the name of the under construction workflow
     *
     * @return string Name of the workflow
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }
}

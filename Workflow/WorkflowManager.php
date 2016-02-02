<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Exceptions\WorkflowNotFoundException;
use TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder;

/**
 * Service that manages the defined workflows
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class WorkflowManager
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Map of workflow definition keyed by name
     *
     * @var array
     */
    private $workflowDefinitions;

    /**
     * Map of built workflows keyed by name
     *
     * @var array
     */
    private $workflows;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     */
    public function __construct()
    {
        // Init
        $this->workflowDefinitions = array();
        $this->workflows = array();
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Adds a workflow to the manager
     *
     * @param AbstractWorkflowDefinition $workflowDefinition Workflow object
     *
     * @return self
     */
    public function addWorkflowDefinition(AbstractWorkflowDefinition $workflowDefinition)
    {
        // Check that the name hasnt been used already
        if (array_key_exists($workflowDefinition->getName(), $this->workflowDefinitions)) {
            throw new WorkflowNameAlreadyUsedException($workflowDefinition->getName());
        } else {
            $this->workflowDefinitions[$workflowDefinition->getName()] = $workflowDefinition;
        }

        return $this;
    }

    /**
     * Get a workflow by its name
     *
     * @param  string $name Name that the workflow is referenced by
     *
     * @return AbstractWorkflow Workflow with the given if exists
     */
    public function getWorkflow($name)
    {
        // First check if the workflow is in the the built list
        if (array_key_exists($name, $this->workflows)) {
            return $this->workflows[$name];
        } elseif (array_key_exists($name, $this->workflowDefinitions)) {
            // If not check if the definition is in the list, and build it if it is
            $builder = new WorkflowBuilder($this->workflowDefinitions[$name]->getName());
            $builder->contextClass($this->workflowDefinitions[$name]->getContextClass());
            $builder = $this->workflowDefinitions[$name]->build($builder);
            $this->workflows[$name] = $builder->build();
            return $this->workflows[$name];
        } else {
            // If not that either, throw error
            throw new WorkflowNotFoundException($name, array_keys($this->workflowDefinitions));
        }
    }

    /**
     * Get all the definitions in the manager
     *
     * @return array Array of definitions in the manager
     */
    public function getDefinitions()
    {
        return $this->workflowDefinitions;
    }

    /**
     * Get all the states with time limits
     *
     * WARNING! this will build all the workflows!!!
     *
     * @return array Array of TimeLimitChecks
     */
    public function getTimeLimitedStateChecks()
    {
        $timeLimitChecks = array();
        foreach(array_keys($this->workflowDefinitions) as $workflowName) {
            foreach($this->getWorkflow($workflowName)->getTimeLimitedStates() as $state) {
                $timeLimitChecks[] = new TimeLimitCheck($workflowName, $state->getName(), $state->getTimeLimit()->getTimeLimit());
            }
        }
        return $timeLimitChecks;
    }
}

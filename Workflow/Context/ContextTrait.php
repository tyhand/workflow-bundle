<?php

namespace TyHand\WorkflowBundle\Workflow\Context;

use Doctrine\Common\Collections\ArrayCollection;
use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

/**
 * Trait implementing the methods from the context interface
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
trait ContextTrait
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Doctrine collection of workflow instances
     *
     * @var ArrayCollection
     */
    protected $workflowInstances;

    /////////////
    // METHODS //
    /////////////

    /**
     * Add a workflow instance
     *
     * @param WorkflowInstanceEntity $workflowInstance Instance entity to add
     *
     * @return self
     */
    public function addWorkflowInstance(WorkflowInstanceEntity $workflowInstance)
    {
        // Check if workflow instances is initialized
        if (null === $this->workflowInstances) {
            $this->workflowInstances = new ArrayCollection();
        }

        $this->workflowInstances[] = $workflowInstance;
        return $this;
    }

    /**
     * Remove a workflow instance
     *
     * @param  WorkflowInstanceEntity $workflowInstance Instance entity to remove
     *
     * @return self
     */
    public function removeWorkflowInstance(WorkflowInstanceEntity $workflowInstance)
    {
        // Check if workflow instances is initialized
        if (null === $this->workflowInstances) {
            $this->workflowInstances = new ArrayCollection();
        }

        $this->workflowInstances->removeElement($workflowInstance);
        return $this;
    }

    /**
     * Get the array collection of workflow instances
     *
     * @return ArrayCollection Doctrine collection
     */
    public function getWorkflowInstances()
    {
        // Check if workflow instances is initialized
        if (null === $this->workflowInstances) {
            $this->workflowInstances = new ArrayCollection();
        }

        return $this->workflowInstances;
    }

    /**
     * Get a workflow instance by workflow name
     *
     * @param  string  $workflowName   Name of the workflow
     * @param  boolean $onlyIncomplete (default: false) Show only incomplete instances
     *
     * @return ArrayCollection Workflow instance entity collection for the workflow if exists
     */
    public function getWorkflowInstancesForWorkflow($workflowName, $onlyIncomplete = false)
    {
        // Get the instances via the get function to make sure the instance collection
        // is initialized
        return $this->getWorkflowInstances()->filter(function($instance) use ($workflowName, $onlyIncomplete) {
            if ($onlyIncomplete && $instance->isComplete()) {
                return false;
            }
            return ($workflowName === $instance->getWorkflowName());
        });
    }

    /**
     * Check if workflow instances for a particular workflow exist
     *
     * @param  string  $workflowName   Name of the workflow
     * @param  boolean $onlyIncomplete (default: false) Show only incomplete instances
     *
     * @return boolean Weather such instances exist
     */
    public function hasWorkflowInstancesForWorkflow($workflowName, $onlyIncomplete = false)
    {
        return (0 < $this->getWorkflowInstancesForWorkflow($workflowName, $onlyIncomplete)->count());
    }
}

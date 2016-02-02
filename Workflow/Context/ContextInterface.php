<?php

namespace TyHand\WorkflowBundle\Workflow\Context;

use Doctrine\Common\Collections\ArrayCollection;
use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

/**
 * Interface defining the methods an object needs to implement to be a context
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
interface ContextInterface
{
    /**
     * Add a workflow instance
     *
     * @param WorkflowInstanceEntity $workflowInstance Instance entity to add
     *
     * @return self
     */
    public function addWorkflowInstance(WorkflowInstanceEntity $workflowInstance);

    /**
     * Remove a workflow instance
     *
     * @param  WorkflowInstanceEntity $workflowInstance Instance entity to remove
     *
     * @return self
     */
    public function removeWorkflowInstance(WorkflowInstanceEntity $workflowInstance);

    /**
     * Get the array collection of workflow instances
     *
     * @return ArrayCollection Doctrine collection
     */
    public function getWorkflowInstances();

    /**
     * Get a workflow instance by workflow name
     *
     * @param  string  $workflowName   Name of the workflow
     * @param  boolean $onlyIncomplete (default: false) Show only incomplete instances
     *
     * @return ArrayCollection Workflow instance entity collection for the workflow if exists
     */
    public function getWorkflowInstancesForWorkflow($workflowName, $onlyIncomplete = false);

    /**
     * Check if workflow instances for a particular workflow exist
     *
     * @param  string  $workflowName   Name of the workflow
     * @param  boolean $onlyIncomplete (default: false) Show only incomplete instances
     *
     * @return boolean Weather such instances exist
     */
    public function hasWorkflowInstancesForWorkflow($workflowName, $onlyIncomplete = false);
}

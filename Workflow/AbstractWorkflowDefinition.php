<?php

namespace TyHand\WorkflowBundle\Workflow;

use TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder;

/**
 * Abstract class that serves as the basis for all workflows
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
abstract class AbstractWorkflowDefinition
{
    //////////////////////
    // ABSTRACT METHODS //
    //////////////////////

    /**
     * Get the name of the workflow (must be unique in the workflow manager)
     *
     * @return string Workflow Name
     */
    abstract public function getName();

    /**
     * Get the class name of the workflow context object
     *
     * @return string Context object of the workflow
     */
    abstract public function getContextClass();

    /**
     * Build a workflow from the definition
     *
     * @param  WorkflowBuilder $builder Workflow Builder
     *
     * @return WorkflowBuilder Finalized builder
     */
    abstract public function build(WorkflowBuilder $builder);
}

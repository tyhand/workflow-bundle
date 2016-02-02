<?php

namespace TyHand\WorkflowBundle\Listeners;

use TyHand\WorkflowBundle\Events\WorkflowEvent;
use TyHand\WorkflowBundle\Workflow\WorkflowManager;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Listens for the workflow event event
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class WorkflowEventListener
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Workflow manager
     *
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * Object manager
     *
     * @var ObjectManager
     */
    private $objectManager;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param WorkflowManager $workflowManager Workflowmanager
     */
    public function __construct(WorkflowManager $workflowManager, ObjectManager $objectManager)
    {
        // Set
        $this->workflowManager = $workflowManager;
        $this->objectManager = $objectManager;
    }

    ///////////////////
    // EVENT METHODS //
    ///////////////////

    /**
     * Handle the state changes for a workflow event
     *
     * @param  WorkflowEvent $event Workflow event
     */
    public function onWorkflowEvent(WorkflowEvent $event)
    {
        // Get the workflow specified in the context
        $workflow = $this->workflowManager->getWorkflow($event->getWorkflowName());

        // Get all incomplete instances for the context in this workflow
        $instances = $event->getContext()->getWorkflowInstancesForWorkflow($event->getWorkflowName(), true);

        // Foreach each instance, check for a move
        foreach($instances as $instance) {
            $state = $workflow->getState($instance->getStateName());
            if (array_key_exists($event->getEventName(), $state->getEvents())) {
                $instance = $state->getEvents()[$event->getEventName()]->getState()->moveTo($event->getContext(), $instance);
                $this->objectManager->flush($instance);
            }
        }
    }
}

<?php

namespace TyHand\WorkflowBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use TyHand\WorkflowBundle\Workflow\Context\ContextInterface;

/**
 * Workflow event
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class WorkflowEvent extends Event
{
    ///////////////
    // CONSTANTS //
    ///////////////

    // Name of the event
    const WORKFLOW_EVENT = 'tyhand_workflow.workflow_event';

    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Context of the event
     *
     * @var ContextInterface
     */
    private $context;

    /**
     * Name of the workflow the event is for
     *
     * @var string
     */
    private $workflowName;

    /**
     * Name of the event
     *
     * @var string
     */
    private $eventName;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param ContextInterface $context      Context of the event and workflow
     * @param string           $workflowName Name of the workflow the event is for
     * @param string           $eventName    Name of the event
     */
    public function __construct(ContextInterface $context, $workflowName, $eventName)
    {
        // Set
        $this->context = $context;
        $this->workflowName = $workflowName;
        $this->eventName = $eventName;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Context of the event
     *
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get the value of Name of the workflow the event is for
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Get the value of Name of the event
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

}

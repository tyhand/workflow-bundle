<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when a context does not match the context class of the workflow
 */
class ContextNotAcceptedByWorkflowException extends RuntimeException
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Context class that caused the exception
     *
     * @var string
     */
    private $contextClass;

    /**
     * Context class the workflow was expecting
     *
     * @var string
     */
    private $workflowContextClass;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string     $contextClass         Context class that caused the exception
     * @param string     $workflowContextClass Missing interface
     * @param \Exception $previous             Optional previous exception
     */
    public function __construct($contextClass, $workflowContextClass, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'Workflow expecting context of class "%s" but got a context of class "%s"',
                $workflowContextClass,
                $contextClass
            ),
            0,
            $previous
        );

        // Set the properties
        $this->contextClass = $contextClass;
        $this->workflowContextClass = $workflowContextClass;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of Context class that caused the exception
     *
     * @return string
     */
    public function getContextClass()
    {
        return $this->contextClass;
    }

    /**
     * Get the class the workflow accepts
     *
     * @return string
     */
    public function getWorkflowContextClass()
    {
        return $this->workflowContextClass;
    }
}

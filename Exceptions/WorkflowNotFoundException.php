<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when a workflow is requested that does not appear to exist
 */
class WorkflowNotFoundException extends RuntimeException
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Requested workflow name
     *
     * @var string
     */
    private $requestedWorkflowName;

    /**
     * Array of known workflow keys
     *
     * @var array
     */
    private $knownWorkflowNames;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string     $requestedWorkflowName Name that caused the exception
     * @param array      $knownWorkflowNames    List of known keys
     * @param \Exception $previous             Optional previous exception
     */
    public function __construct($requestedWorkflowName, array $knownWorkflowNames, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'Workflow with name "%s" was not found.  Names found are [ "%s" ]',
                $requestedWorkflowName,
                implode('", "', $knownWorkflowNames)
            ),
            0,
            $previous
        );

        // Set the properties
        $this->requestedWorkflowName = $requestedWorkflowName;
        $this->knownWorkflowNames = $knownWorkflowNames;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of Requested workflow name
     *
     * @return string
     */
    public function getRequestedWorkflowName()
    {
        return $this->requestedWorkflowName;
    }

    /**
     * Get the value of Array of known workflow keys
     *
     * @return array
     */
    public function getKnownWorkflowNames()
    {
        return $this->knownWorkflowNames;
    }
}

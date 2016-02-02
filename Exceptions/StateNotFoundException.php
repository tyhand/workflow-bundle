<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when a state is requested that does not appear to exist
 */
class StateNotFoundException extends RuntimeException
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Requested workflow name
     *
     * @var string
     */
    private $requestedStateName;

    /**
     * Array of known workflow keys
     *
     * @var array
     */
    private $knownStateNames;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string     $requestedStateName Name that caused the exception
     * @param array      $knownStateNames    List of known keys
     * @param \Exception $previous           Optional previous exception
     */
    public function __construct($requestedStateName, array $knownStateNames, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'State with name "%s" was not found.  Names found are [ "%s" ]',
                $requestedStateName,
                implode('", "', $knownStateNames)
            ),
            0,
            $previous
        );

        // Set the properties
        $this->requestedStateName = $requestedStateName;
        $this->knownStateNames = $knownStateNames;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of Requested workflow name
     *
     * @return string
     */
    public function getRequestedStateName()
    {
        return $this->requestedStateName;
    }

    /**
     * Get the value of Array of known workflow keys
     *
     * @return array
     */
    public function getKnownStateNames()
    {
        return $this->knownStateNames;
    }
}

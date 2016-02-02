<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when two workflows share a name
 */
class WorkflowNameAlreadyUsedException extends RuntimeException
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Duplicate name that caused the exception
     *
     * @var string
     */
    private $duplicatedName;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string     $duplicatedName Name that caused the exception
     * @param \Exception $previous      Optional previous exception
     */
    public function __construct($duplicatedName, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'Workflow name "%s" was already used.',
                $duplicatedName
            ),
            0,
            $previous
        );

        // Set the properties
        $this->duplicatedName = $duplicatedName;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of Duplicate name that caused the exception
     *
     * @return string
     */
    public function getDuplicatedName()
    {
        return $this->duplicatedName;
    }
}

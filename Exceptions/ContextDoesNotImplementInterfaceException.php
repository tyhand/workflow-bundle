<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when a context class does not have the correct interface
 */
class ContextDoesNotImplementInterfaceException extends RuntimeException
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
     * Missing interface
     *
     * @var string
     */
    private $missingInterface;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string     $contextClass     Context class that caused the exception
     * @param string     $missingInterface Missing interface
     * @param \Exception $previous         Optional previous exception
     */
    public function __construct($contextClass, $missingInterface, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'Context class "%s" does not implement required interface "%s"',
                $contextClass,
                $missingInterface
            ),
            0,
            $previous
        );

        // Set the properties
        $this->contextClass = $contextClass;
        $this->missingInterface = $missingInterface;
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
     * Get the value of Missing interface
     *
     * @return string
     */
    public function getMissingInterface()
    {
        return $this->missingInterface;
    }
}

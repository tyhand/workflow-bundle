<?php

namespace TyHand\WorkflowBundle\Exceptions;

/**
 * Exception thrown when a context does not match the context class of the workflow
 */
class ContextOverWorkflowLimitException extends RuntimeException
{
    ///////////////
    // CONSTANTS //
    ///////////////

    const ACTIVE_LIMIT = 'active';
    const TOTAL_LIMIT = 'total';

    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Limit that has been violated
     *
     * @var int
     */
    private $limit;

    /**
     * Type of limit (active or total)
     *
     * @var string
     */
    private $limitType;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param int        $limit     Limit that has been violated
     * @param string     $limitType Type of limit
     * @param \Exception $previous  Optional previous exception
     */
    public function __construct($limit, $limitType, \Exception $previous = null)
    {
        // Call the super constructor
        parent::__construct(
            sprintf(
                'This workflow only allows a context to have %d %s instances',
                $limit,
                $limitType
            ),
            0,
            $previous
        );

        // Set the properties
        $this->limit = $limit;
        $this->limitType = $limitType;
    }

    /////////////
    // GETTERS //
    /////////////

    /**
     * Get the value of Limit that has been violated
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the value of Type of limit (active or total)
     *
     * @return string
     */
    public function getLimitType()
    {
        return $this->limitType;
    }
}

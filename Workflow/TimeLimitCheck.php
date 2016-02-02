<?php

namespace TyHand\WorkflowBundle\Workflow;

/**
 * Simple object to pass data to the time limit check command
 */
class TimeLimitCheck
{
    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * Name of the workflow the state is in
     *
     * @var string
     */
    private $workflowName;

    /**
     * Name of the timelimited state
     *
     * @var string
     */
    private $stateName;

    /**
     * DateTime that is the start of the non-overdue times
     *
     * @var \DateTime
     */
    private $earliestStateTime;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     *
     * @param string $workflowName Name of the workflow
     * @param string $stateName    Name of the state
     * @param int    $timeLimit     Time limit in seconds
     */
    public function __construct($workflowName, $stateName, $timeLimit)
    {
        // Set
        $this->workflowName = $workflowName;
        $this->stateName = $stateName;

        // Figure out the earliest non-overdue time
        $this->earliestStateTime = new \DateTime();
        $this->earliestStateTime->modify('-' . $timeLimit . ' seconds');
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Name of the workflow the state is in
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Set the value of Name of the workflow the state is in
     *
     * @param string workflowName
     *
     * @return self
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;
        return $this;
    }

    /**
     * Get the value of Name of the timelimited state
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set the value of Name of the timelimited state
     *
     * @param string stateName
     *
     * @return self
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;
        return $this;
    }

    /**
     * Get the value of DateTime that is the start of the non-overdue times
     *
     * @return \DateTime
     */
    public function getEarliestStateTime()
    {
        return $this->earliestStateTime;
    }

    /**
     * Set the value of DateTime that is the start of the non-overdue times
     *
     * @param \DateTime earliestStateTime
     *
     * @return self
     */
    public function setEarliestStateTime(\DateTime $earliestStateTime)
    {
        $this->earliestStateTime = $earliestStateTime;
        return $this;
    }

}

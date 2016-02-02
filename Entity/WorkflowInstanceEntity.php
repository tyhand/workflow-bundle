<?php

namespace TyHand\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity representing a contexts workflow state
 *
 * @author Ty Hand <https://github.com/tyhand>
 *
 * @ORM\Entity(repositoryClass="TyHand\WorkflowBundle\Repository\WorkflowInstanceRepository")
 * @ORM\Table(name="tyhand_workflow_instance")
 */
class WorkflowInstanceEntity
{
    ////////////////
    // ORM FIELDS //
    ////////////////

    /**
     * Unique database id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Name of the workflow the instance is in
     *
     * @ORM\Column(type="string", name="workflow_name", nullable=false)
     */
    protected $workflowName;

    /**
     * Name of the state the instance is in
     *
     * @ORM\Column(type="string", name="state_name", nullable=false)
     */
    protected $stateName;

    /**
     * Whether the current state is a terminal state
     *
     * @ORM\Column(type="boolean", name="is_complete", nullable=false)
     */
    protected $isComplete;

    /**
     * Date and time when the context entered the current state
     *
     * @ORM\Column(type="datetime", name="state_date", nullable=false)
     */
    protected $stateDate;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor to set the defaults
     */
    public function __construct()
    {
        $this->isComplete = false;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of Unique database id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of Name of the workflow the instance is in
     *
     * @return mixed
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Set the value of Name of the workflow the instance is in
     *
     * @param mixed workflowName
     *
     * @return self
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;
        return $this;
    }

    /**
     * Get the value of Name of the state the instance is in
     *
     * @return mixed
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set the value of Name of the state the instance is in
     *
     * @param mixed stateName
     *
     * @return self
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;
        return $this;
    }

    /**
     * Get the value of Whether the current state is a terminal state
     *
     * @return mixed
     */
    public function getIsComplete()
    {
        return $this->isComplete;
    }

    /**
     * Alias for getIsComplete()
     *
     * @return boolean Whether the instance is a complete journey through the wf
     */
    public function isComplete()
    {
        return $this->getIsComplete();
    }

    /**
     * Set the value of Whether the current state is a terminal state
     *
     * @param mixed isComplete
     *
     * @return self
     */
    public function setIsComplete($isComplete)
    {
        $this->isComplete = $isComplete;
        return $this;
    }

    /**
     * Get the value of Date and time when the context entered the current state
     *
     * @return mixed
     */
    public function getStateDate()
    {
        return $this->stateDate;
    }

    /**
     * Set the value of Date and time when the context entered the current state
     *
     * @param mixed stateDate
     *
     * @return self
     */
    public function setStateDate($stateDate)
    {
        $this->stateDate = $stateDate;
        return $this;
    }

}

<?php

namespace TyHand\WorkflowBundle\Tests\Entity;

use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

class WorkflowInstanceEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $instance = new WorkflowInstanceEntity();
        $this->assertFalse($instance->isComplete());
    }

    public function testGetSetWorkflowName()
    {
        $instance = new WorkflowInstanceEntity();
        $this->assertNull($instance->getWorkflowName());
        $instance->setWorkflowName('testtesttest');
        $this->assertEquals('testtesttest', $instance->getWorkflowName());
    }

    public function testGetSetStateName()
    {
        $instance = new WorkflowInstanceEntity();
        $this->assertNull($instance->getStateName());
        $instance->setStateName('state');
        $this->assertEquals('state', $instance->getStateName());
    }

    public function testGetSetIsComplete()
    {
        $instance = new WorkflowInstanceEntity();
        $this->assertFalse($instance->getIsComplete());
        $this->assertFalse($instance->isComplete());
        $instance->setIsComplete(true);
        $this->assertTrue($instance->getIsComplete());
        $this->assertTrue($instance->isComplete());
        $instance->setIsComplete(false);
        $this->assertFalse($instance->getIsComplete());
        $this->assertFalse($instance->isComplete());
    }

    public function testGetSetStateDate()
    {
        $instance = new WorkflowInstanceEntity();
        $this->assertNull($instance->getStateDate());
        $date = new \DateTime();
        $date->modify('-1 month');
        $instance->setStateDate($date);
        $this->assertEquals($date, $instance->getStateDate());
    }
}

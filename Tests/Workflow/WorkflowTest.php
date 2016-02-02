<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\Workflow;
use TyHand\WorkflowBundle\Workflow\State;
use TyHand\WorkflowBundle\Workflow\EventTrigger;
use TyHand\WorkflowBundle\Tests\DummyContext;

class WorkflowTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckType()
    {
        // Create a new workflow
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $context = new DummyContext();
        $this->assertTrue($workflow->checkType($context));
        $this->assertFalse($workflow->checkType(new \DateTime()));
        $this->assertFalse($workflow->checkType(null));
    }

    public function testGetName()
    {
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $this->assertEquals('test', $workflow->getName());
    }

    public function testGetContextClass()
    {
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $this->assertEquals('TyHand\WorkflowBundle\Tests\DummyContext', $workflow->getContextClass());
    }

    public function testAddGetState()
    {
        $workflow = new Workflow('test', 'null');
        $this->assertCount(0, $workflow->getStateNames());
        $workflow->addState(new State('one'));
        $this->assertCount(1, $workflow->getStateNames());
        $this->assertContains('one', $workflow->getStateNames());
        $this->assertNotNull($workflow->getState('one'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $workflow->getState('one'));
        $this->assertEquals('one', $workflow->getState('one')->getName());
        $workflow->addState(new State('two'));
        $this->assertCount(2, $workflow->getStateNames());
        $this->assertContains('two', $workflow->getStateNames());
        $this->assertNotNull($workflow->getState('two'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $workflow->getState('two'));
        $this->assertEquals('two', $workflow->getState('two')->getName());
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateNotFoundException
     * @expectedExceptionMessage State with name "three" was not found.  Names found are [ "one", "two" ]
     */
    public function testGetStateNotFoundException()
    {
        $workflow = new Workflow('test', 'null');
        $workflow->addState(new State('one'));
        $workflow->addState(new State('two'));
        $workflow->getState('three');
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateNameAlreadyUsedException
     * @expectedExceptionMessage State name "double" was already used in this workflow.
     */
    public function testAddStateDuplicateNameException()
    {
        $workflow = new Workflow('test', 'null');
        $workflow->addState(new State('double'));
        $workflow->addState(new State('double'));
    }

    public function testGetSetInitialState()
    {
        $workflow = new Workflow('test', 'null');
        $this->assertNull($workflow->getInitialState());
        $workflow->setInitialState(new State('start'));
        $this->assertNotNull($workflow->getInitialState());
        $this->assertEquals('start', $workflow->getInitialState()->getName());
    }

    public function testGetSetActiveInstanceLimit()
    {
        $workflow = new Workflow('test', 'null');
        $this->assertNull($workflow->getActiveInstanceLimit());
        $workflow->setActiveInstanceLimit(5);
        $this->assertNotNull($workflow->getActiveInstanceLimit());
        $this->assertEquals(5, $workflow->getActiveInstanceLimit());
    }

    public function testGetSetTotalInstanceLimit()
    {
        $workflow = new Workflow('test', 'null');
        $this->assertNull($workflow->getTotalInstanceLimit());
        $workflow->setTotalInstanceLimit(5);
        $this->assertNotNull($workflow->getTotalInstanceLimit());
        $this->assertEquals(5, $workflow->getTotalInstanceLimit());
    }

    public function testStart()
    {
        $now = new \DateTime();
        $dummy = new DummyContext();
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $workflow->addState($stateA);
        $workflow->setInitialState($stateA);
        $instance = $workflow->start($dummy);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('A', $instance->getStateName());
        $this->assertEquals('test', $instance->getWorkflowName());
        $this->assertGreaterThanOrEqual($now, $instance->getStateDate());
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\ContextNotAcceptedByWorkflowException
     * @expectedExceptionMessage Workflow expecting context of class "null" but got a context of class "TyHand\WorkflowBundle\Tests\DummyContext"
     */
    public function testStartContextNotAcceptedByWorkflowException()
    {
        $dummy = new DummyContext();
        $workflow = new Workflow('test', 'null');
        $stateA = new State('A');
        $workflow->addState($stateA);
        $workflow->setInitialState($stateA);
        $instance = $workflow->start($dummy);
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\ContextOverWorkflowLimitException
     * @expectedExceptionMessage This workflow only allows a context to have 2 total instances
     */
    public function testStartContextOverTotalWorkflowLimitException()
    {
        $dummy = new DummyContext();
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $workflow->addState($stateA);
        $workflow->setInitialState($stateA);
        $workflow->setTotalInstanceLimit(2);
        $instance = $workflow->start($dummy);

        $instance2 = $workflow->start($dummy);
        $this->assertTrue(true); // Checkpoint check
        $instance3 = $workflow->start($dummy);
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\ContextOverWorkflowLimitException
     * @expectedExceptionMessage This workflow only allows a context to have 2 active instances
     */
    public function testStartContextWorkflowActiveLimitException()
    {
        $dummy = new DummyContext();
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $stateB = new State('B');
        $trigger = new EventTrigger('go', 'B');
        $trigger->setState($stateB);
        $stateA->addEventTrigger($trigger);
        $workflow->addState($stateA);
        $workflow->addState($stateB);
        $workflow->setInitialState($stateA);
        $workflow->setActiveInstanceLimit(2);
        $instance = $workflow->start($dummy);

        // Mark instance 1 to be complete to test that only active is being checked
        $instance->setIsComplete(false);

        $instance2 = $workflow->start($dummy);
        $instance3 = $workflow->start($dummy);
        $this->assertTrue(true); // Checkpoint check
        $instance4 = $workflow->start($dummy);
    }
}

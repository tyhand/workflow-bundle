<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\TimeLimit;
use TyHand\WorkflowBundle\Workflow\State;

class TimeLimitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsComplete()
    {
        // Test that iscomplete returns false until the followup state is set
        $limit = new TimeLimit(60, 'dummy');
        $this->assertFalse($limit->isComplete());

        // Test that adding a state makes this method return true instead
        $dummyState = new State('dummy');
        $limit->setState($dummyState);
        $this->assertTrue($limit->isComplete());
    }

    public function testIsPassed()
    {
        // Test that a 2 second limit passes as expected
        $now = new \DateTime();
        $limit = new TimeLimit(5, 'dummy');
        $this->assertFalse($limit->isPassed($now));
        $now->modify('-6 seconds');
        $this->assertTrue($limit->isPassed($now));
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException
     * @expectedExceptionMessage Trying to set state with name "stop" when object was expecting a state with name "go"
     */
    public function testSetStateNamMismatchException()
    {
        $limit = new TimeLimit(60, 'go');
        $limit->setState(new State('stop'));
    }

    public function testGetSetState()
    {
        $limit = new TimeLimit(60, 'dummy');
        $limit->setState(new State('dummy'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $limit->getState());
        $this->assertEquals('dummy', $limit->getState()->getName());
    }

    public function testGetTimeLimit()
    {
        $limit = new TimeLimit(60, 'dummy');
        $this->assertEquals(60, $limit->getTimeLimit());
    }

    public function testGetStateName()
    {
        $limit = new TimeLimit(60, 'dummy');
        $this->assertEquals('dummy', $limit->getStateName());
    }
}

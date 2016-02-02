<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\Condition;
use TyHand\WorkflowBundle\Workflow\State;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsComplete()
    {
        // Create a condition with no followup states
        $condition = new Condition(function($context) { return true; });
        $this->assertTrue($condition->isComplete());

        // Create a condition with a followup state name but no state
        $condition = new Condition(function($context) { return true; }, 'dummy');
        $this->assertFalse($condition->isComplete());

        // Create a condition with a followup state name and state
        $dummyState = new State('dummy');
        $condition->setTrueState($dummyState);
        $this->assertTrue($condition->isComplete());

        // Create a condition with a complete true state and incomplete false state
        $condition = new Condition(function($context) { return true; }, 'dummy', 'dummy2');
        $condition->setTrueState(new State('dummy'));
        $this->assertFalse($condition->isComplete());

        // Set both the true and false state
        $dummyState2 = new State('dummy2');
        $condition->setFalseState($dummyState2);
        $this->assertTrue($condition->isComplete());
    }

    public function testEvaluate()
    {
        // Base test case
        $condition = new Condition(function ($context) { return $context > 1; }, 'apples', 'oranges');
        $apples = new State('apples');
        $oranges = new State('oranges');
        $condition->setTrueState($apples);
        $condition->setFalseState($oranges);

        $this->assertEquals('apples', $condition->evaluate(2)->getName());
        $this->assertEquals('oranges', $condition->evaluate(0)->getName());

        // Test the result where followup states dont exist
        $condition = new Condition(function ($context) { return $context == 'turtles'; }, 'apples');
        $condition->setTrueState($apples);
        $this->assertNull($condition->evaluate('eels'));
        $this->assertEquals('apples', $condition->evaluate('turtles')->getName());

        // Test the inverse of the above situation
        $condition = new Condition(function ($context) { return !$context; }, null, 'oranges');
        $condition->setFalseState($oranges);
        $this->assertNull($condition->evaluate(false));
        $this->assertEquals('oranges', $condition->evaluate(true)->getName());

        // no followup states?
        $condition = new Condition(function ($context) { return $context === 0; });
        $this->assertNull($condition->evaluate(0));
        $this->assertNull($condition->evaluate(15));
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException
     * @expectedExceptionMessage Trying to set state with name "wrong" when object was expecting a state with name "correct"
     */
    public function testSetTrueStateMismatchException()
    {
        $condition = new Condition(function ($context) { return true; }, 'correct');
        $condition->setTrueState(new State('wrong'));
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException
     * @expectedExceptionMessage Trying to set state with name "blue" when object was expecting a state with name "red"
     */
    public function testSetWrongStateMismatchException()
    {
        $condition = new Condition(function ($context) { return true; }, null, 'red');
        $condition->setFalseState(new State('blue'));
    }

    public function testGetCondition()
    {
        $condition = new Condition(function ($context) { return $context < 5; });
        $callable = $condition->getCondition();
        $this->assertTrue(call_user_func($callable, 4));
        $this->assertFalse(call_user_func($callable, 5));
    }

    public function testGetTrueStateName()
    {
        $condition = new Condition(function ($context) { return true; }, 'next');
        $this->assertEquals('next', $condition->getTrueStateName());
        $condition = new Condition(function ($context) { return true; }, null, 'go');
        $this->assertNull($condition->getTrueStateName());
    }

    public function testGetFalseStateName()
    {
        $condition = new Condition(function ($context) { return true; }, 'next');
        $this->assertNull($condition->getFalseStateName());
        $condition = new Condition(function ($context) { return true; }, null, 'go');
        $this->assertEquals('go', $condition->getFalseStateName());
    }

    public function testGetSetTrueState()
    {
        $condition = new Condition(function ($context) { return true; }, 'new-state');
        $this->assertNull($condition->getTrueState());
        $condition->setTrueState(new State('new-state'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $condition->getTrueState());
        $this->assertEquals('new-state', $condition->getTrueState()->getName());
    }

    public function testGetSetFalseState()
    {
        $condition = new Condition(function ($context) { return false; }, 'nowhere', 'somewhere');
        $this->assertNull($condition->getFalseState());
        $condition->setFalseState(new State('somewhere'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $condition->getFalseState());
        $this->assertEquals('somewhere', $condition->getFalseState()->getName());
    }
}

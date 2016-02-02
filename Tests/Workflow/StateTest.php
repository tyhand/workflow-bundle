<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\State;
use TyHand\WorkflowBundle\Workflow\Condition;
use TyHand\WorkflowBundle\Workflow\TimeLimit;
use TyHand\WorkflowBundle\Workflow\EventTrigger;
use TyHand\WorkflowBundle\Tests\DummyContext;
use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

/**
 * Actual Test
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    public function testCallActions()
    {
        // Base Case
        $state = new State('dummy');
        $state->addAction(function($context) { $context->increment(); });
        $context = new DummyContext();
        $state->callActions($context);
        $this->assertEquals(2, $context->getNum());

        // Check ordering
        $state = new State('fancy');
        $state->addAction(function($context) { $context->increment(); });
        $state->addAction(function($context) { $context->doublify(); });
        $context = new DummyContext();
        $state->callActions($context);
        $this->assertEquals(4, $context->getNum());
    }

    public function testEvaluateConditions()
    {
        // Base Case
        $state = new State('dummy');
        $conditionOne = new Condition(function($context) { return $context->getLabel() === 'fresh'; }, 'one');
        $conditionTwo = new Condition(function($context) { return $context->getNum() <= 1; }, 'two', 'three');
        $one = new State('one');
        $two = new State('two');
        $three = new State('three');
        $conditionOne->setTrueState($one);
        $conditionTwo->setTrueState($two);
        $conditionTwo->setFalseState($three);
        $state->addCondition($conditionOne);
        $state->addCondition($conditionTwo);

        $context = new DummyContext();
        $context->setLabel('fresh');
        // One
        $this->assertEquals('one', $state->evaluateConditions($context)->getName());
        // Two
        $context->setLabel('soggy');
        $this->assertEquals('two', $state->evaluateConditions($context)->getName());
        // Three
        $context->increment();
        $this->assertEquals('three', $state->evaluateConditions($context)->getName());
    }

    public function testGetName()
    {
        $state = new State('test');
        $this->assertEquals('test', $state->getName());
    }

    public function testSetHasTimeLimit()
    {
        $state = new State('test');
        $this->assertFalse($state->hasTimeLimit());
        $state->setTimeLimit(new TimeLimit(60, 'nowhere'));
        $this->assertTrue($state->hasTimeLimit());
    }

    /**
     * @depends testSetHasTimeLimit
     */
    public function testHasTimeLimitPassed()
    {
        $state = new State('limited');
        $limit = new TimeLimit(60, 'outoftime');
        $limit->setState(new State('outoftime'));
        $time = new \DateTime();
        $this->assertNull($state->hasTimeLimitPassed($time));
        $state->setTimeLimit($limit);
        $this->assertNull($state->hasTimeLimitPassed($time));
        $time->modify('-1 hour');
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $state->hasTimeLimitPassed($time));
        $this->assertEquals('outoftime', $state->hasTimeLimitPassed($time)->getName());
    }

    public function testIsTerminal()
    {
        $state = new State('node');
        $this->assertTrue($state->isTerminal());
        $state->setTimeLimit(new TimeLimit(60, 'next'));
        $this->assertFalse($state->isTerminal());

        $state = new State('node');
        $state->addEventTrigger(new EventTrigger('go', 'next'));
        $this->assertFalse($state->isTerminal());

        // Conditions will register as terminals, so the conditions and movements
        // from them will be used before the terminal check
        $state = new State('node');
        $state->addCondition(new Condition(function($context) { return true;}, 'next'));
        $this->assertTrue($state->isTerminal());
    }

    public function testGetSetEvents()
    {
        $state = new State('test');
        $this->assertCount(0, $state->getEvents());
        $state->addEventTrigger(new EventTrigger('go', 'B'));
        $this->assertCount(1, $state->getEvents());
        $this->assertArrayHasKey('go', $state->getEvents());
        $this->assertEquals('B', $state->getEvents()['go']->getStateName());
        $state->addEventTrigger(new EventTrigger('skip', 'C'));
        $this->assertCount(2, $state->getEvents());
        $this->assertArrayHasKey('go', $state->getEvents());
        $this->assertArrayHasKey('skip', $state->getEvents());
        $this->assertEquals('B', $state->getEvents()['go']->getStateName());
        $this->assertEquals('C', $state->getEvents()['skip']->getStateName());
    }

    public function testMoveTo()
    {
        $dummy = new DummyContext();
        $start = new \DateTime();

        // Test Simple Terminal State
        $instance = new WorkflowInstanceEntity();
        $state = new State('one');
        $instance = $state->moveTo($dummy, $instance);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('one', $instance->getStateName());
        $this->assertGreaterThanOrEqual($start, $instance->getStateDate());
        $this->assertTrue($instance->isComplete());

        // Test Simple NonTerminal State
        $instance = new WorkflowInstanceEntity();
        $state = new State('two');
        $stateB = new State('B');
        $trigger = new EventTrigger('go', 'B');
        $trigger->setState($stateB);
        $state->addEventTrigger($trigger);
        $instance = $state->moveTo($dummy, $instance);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('two', $instance->getStateName());
        $this->assertGreaterThanOrEqual($start, $instance->getStateDate());
        $this->assertFalse($instance->isComplete());

        // Test State with Actions
        $instance = new WorkflowInstanceEntity();
        $state = new State('three');
        $state->addAction(function($context) { $context->increment(); });
        $this->assertEquals(1, $dummy->getNum());
        $instance = $state->moveTo($dummy, $instance);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('three', $instance->getStateName());
        $this->assertGreaterThanOrEqual($start, $instance->getStateDate());
        $this->assertEquals(2, $dummy->getNum());
        $this->assertTrue($instance->isComplete());

        // Test State with Conditions that eval to true
        $instance = new WorkflowInstanceEntity();
        $state = new State('four');
        $stateB = new State('B');
        $stateC = new State('C');
        $condition = new Condition(function($context) { return true; }, 'B', 'C');
        $condition->setTrueState($stateB);
        $condition->setFalseState($stateC);
        $state->addCondition($condition);
        $instance = $state->moveTo($dummy, $instance);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('B', $instance->getStateName());
        $this->assertGreaterThanOrEqual($start, $instance->getStateDate());
        $this->assertTrue($instance->isComplete());

        // Test State with conditions that eval to false
        $instance = new WorkflowInstanceEntity();
        $state = new State('four');
        $stateB = new State('B');
        $stateC = new State('C');
        $condition = new Condition(function($context) { return false; }, 'B', 'C');
        $condition->setTrueState($stateB);
        $condition->setFalseState($stateC);
        $state->addCondition($condition);
        $instance = $state->moveTo($dummy, $instance);
        $this->assertNotNull($instance);
        $this->assertInstanceOf('TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $instance);
        $this->assertEquals('C', $instance->getStateName());
        $this->assertGreaterThanOrEqual($start, $instance->getStateDate());
        $this->assertTrue($instance->isComplete());
    }
}

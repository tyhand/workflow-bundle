<?php

namespace TyHand\WorkflowBundle\Tests\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder;
use TyHand\WorkflowBundle\Workflow\Builder\StateBuilder;
use TyHand\WorkflowBundle\Workflow\State;
use TyHand\WorkflowBundle\Tests\DummyContext;

class StateBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testEnd()
    {
        $workflowBuilder = new WorkflowBuilder('dummy', 'null');
        $stateBuilder = new StateBuilder($workflowBuilder, 'test');

        $this->assertNotNull($stateBuilder->end());
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder', $stateBuilder->end());
        $this->assertEquals('dummy', $stateBuilder->end()->getWorkflowName());
    }

    public function testStartCondition()
    {
        $workflowBuilder = new WorkflowBuilder('dummy', 'null');
        $stateBuilder = new StateBuilder($workflowBuilder, 'test');

        $conditionBuilder = $stateBuilder->startCondition();

        $this->assertNotNull($conditionBuilder);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\Builder\ConditionBuilder', $conditionBuilder);
        $this->assertEquals($stateBuilder, $conditionBuilder->end());
    }

    public function testBuild()
    {
        // Test build with actions
        $workflowBuilder = new WorkflowBuilder('testflow', '\TyHand\WorkflowBundle\Tests\DummyContext');
        $stateBuilder = new StateBuilder($workflowBuilder, 'round1');
        $stateBuilder->addAction(function ($context) { $context->increment(); });
        $state = $stateBuilder->build(array());
        $this->assertNotNull($state);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $state);
        $this->assertEquals('round1', $state->getName());
        $this->assertCount(1, $state->getActions());
        $testContext = new DummyContext();
        $this->assertEquals(1, $testContext->getNum());
        call_user_func($state->getActions()[0], $testContext, $state);
        $this->assertEquals(2, $testContext->getNum());
        $this->assertCount(0, $state->getEvents());
        $this->assertFalse($state->hasTimeLimit());

        // Test build with events
        $stateBuilder = new StateBuilder($workflowBuilder, 'round2');
        $stateBuilder->addEvent('knockout', 'finish');
        $state = $stateBuilder->build(array('finish' => new State('finish')));
        $this->assertNotNull($state);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $state);
        $this->assertEquals('round2', $state->getName());
        $this->assertCount(1, $state->getEvents());
        $this->assertArrayHasKey('knockout', $state->getEvents());
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\EventTrigger', $state->getEvents()['knockout']);
        $this->assertNotNull($state->getEvents()['knockout']->getState());
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $state->getEvents()['knockout']->getState());
        $this->assertEquals('finish', $state->getEvents()['knockout']->getState()->getName());
        $this->assertCount(0, $state->getActions());
        $this->assertFalse($state->hasTimeLimit());

        // Test build with time limit
        $stateBuilder = new StateBuilder($workflowBuilder, 'round3');
        $stateBuilder->setTimeLimit(60, 'gameover');
        $state = $stateBuilder->build(array('gameover' => new State('gameover')));
        $this->assertNotNull($state);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $state);
        $this->assertEquals('round3', $state->getName());
        $this->assertTrue($state->hasTimeLimit());
        $this->assertNotNull($state->getTimeLimit());
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\TimeLimit', $state->getTimeLimit());
        $this->assertTrue($state->getTimeLimit()->isComplete());
        $now = new \DateTime();
        $now->modify('-60 seconds');
        $nextState = $state->hasTimeLimitPassed($now);
        $this->assertNotNull($nextState);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $nextState);
        $this->assertEquals('gameover', $nextState->getName());
        $this->assertCount(0, $state->getEvents());
        $this->assertCount(0, $state->getActions());
    }
}

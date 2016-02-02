<?php

namespace TyHand\WorkflowBundle\Tests\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\Builder\ConditionBuilder;
use TyHand\WorkflowBundle\Workflow\Builder\StateBuilder;
use TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder;
use TyHand\WorkflowBundle\Workflow\State;

class ConditionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        // Create the base workflow builder to create the state builder to test
        // the condition builder
        $workflowBuilder = new WorkflowBuilder('test', 'null');
        $stateBuilder = new StateBuilder($workflowBuilder, 'dummy');
        $conditionBuilder = new ConditionBuilder($stateBuilder);

        // Construct a base case condition
        $conditionBuilder->conditionFunction(function ($context) { return $context < 5; });
        $conditionBuilder->ifTrue('apples');
        $conditionBuilder->ifFalse('oranges');
        $stateMap = array('apples' => new State('apples'), 'oranges' => new State('oranges'));

        $condition = $conditionBuilder->build($stateMap);

        $this->assertInstanceOf('TyHand\WorkflowBundle\Workflow\Condition', $condition);
        $this->assertNotNull($condition->getTrueState());
        $this->assertNotNull($condition->getFalseState());
        $this->assertEquals('apples', $condition->getTrueState()->getName());
        $this->assertEquals('oranges', $condition->getFalseState()->getName());

        $this->assertEquals('apples', $condition->evaluate(1)->getName());
        $this->assertEquals('oranges', $condition->evaluate(6)->getName());

        // Test a condition with only one followup state
        $conditionBuilder = new ConditionBuilder($stateBuilder);
        $conditionBuilder->conditionFunction(function ($context) { return $context < 5; });
        $conditionBuilder->ifTrue('apples');

        $condition = $conditionBuilder->build($stateMap);
        $this->assertEquals('apples', $condition->getTrueState()->getName());
        $this->assertNull($condition->getFalseState());
        $this->assertEquals('apples', $condition->evaluate(1)->getName());
        $this->assertNull($condition->evaluate(6));
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateNotFoundException
     * @expectedExceptionMessage State with name "bananas" was not found.  Names found are [ "apples", "oranges" ]
     */
    public function testBuildStateNotFoundException()
    {
        // Test that the state not found exception when no existent state is requested
        $workflowBuilder = new WorkflowBuilder('test', 'null');
        $stateBuilder = new StateBuilder($workflowBuilder, 'dummy');
        $conditionBuilder = new ConditionBuilder($stateBuilder);

        // Construct a base case condition
        $conditionBuilder->conditionFunction(function ($context) { return $context < 5; });
        $conditionBuilder->ifTrue('apples');
        $conditionBuilder->ifFalse('bananas');
        $stateMap = array('apples' => new State('apples'), 'oranges' => new State('oranges'));

        $condition = $conditionBuilder->build($stateMap);
    }

    public function testEnd()
    {
        // Create the condition builder
        $workflowBuilder = new WorkflowBuilder('test', 'null');
        $stateBuilder = new StateBuilder($workflowBuilder, 'dummy');
        $conditionBuilder = new ConditionBuilder($stateBuilder);

        // Assert that the state builder is returned from end
        $this->assertInstanceOf('TyHand\WorkflowBundle\Workflow\Builder\StateBuilder', $conditionBuilder->end());
        $this->assertEquals('dummy', $conditionBuilder->end()->getStateName());
    }
}

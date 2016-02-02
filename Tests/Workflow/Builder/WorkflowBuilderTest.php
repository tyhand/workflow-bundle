<?php

namespace TyHand\WorkflowBundle\Tests\Workflow\Builder;

use TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder;
use TyHand\WorkflowBundle\Tests\DummyContext;

class WorkflowBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateNotFoundException
     * @expectedExceptionMessage State with name "nowhere" was not found.  Names found are [ "" ]
     */
    public function testBuildIntialStateNotFoundException()
    {
        $builder = new WorkflowBuilder('test');
        $builder->initial('nowhere');
        $workflow = $builder->build();
    }

    public function testBuild()
    {
        $builder = new WorkflowBuilder('test');
        $workflow = $builder
            ->initial('one')
            ->contextClass('TyHand\WorkflowBundle\Tests\DummyContext')
            ->startState('one')
                ->setTimeLimit(60, 'two')
            ->end()
            ->startState('two')
            ->end()
            ->build()
        ;

        $this->assertNotNull($workflow);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\Workflow', $workflow);
        $this->assertTrue($workflow->checkType(new DummyContext()));
        $this->assertEquals('one', $workflow->getState('one')->getName());
        $this->assertEquals('two', $workflow->getState('two')->getName());
        $this->assertNotNull($workflow->getInitialState());
        $this->assertEquals('one', $workflow->getInitialState()->getName());
        $this->assertEquals(2, $workflow->getNumberOfStates());
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\ContextDoesNotImplementInterfaceException
     * @expectedExceptionMessage Context class "DateTime" does not implement required interface "TyHand\WorkflowBundle\Workflow\Context\ContextInterface"
     */
    public function testContextClassMissingInterfaceException()
    {
        $builder = new WorkflowBuilder('test');
        $builder->contextClass('TyHand\WorkflowBundle\Tests\DummyContext');
        $builder->contextClass('DateTime');
    }

    public function testStartState()
    {
        $builder = new WorkflowBuilder('test');
        $stateBuilder = $builder->startState('round1');
        $this->assertNotNull($stateBuilder);
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\Builder\StateBuilder', $stateBuilder);
        $this->assertEquals($builder, $stateBuilder->end());
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateNameAlreadyUsedException
     * @expectedExceptionMessage State name "duplicate_test" was already used in this workflow.
     */
    public function testStartStateDuplicateNameException()
    {
        $builder = new WorkflowBuilder('test');
        $stateBuilder1 = $builder->startState('duplicate_test');
        $stateBuilder2 = $builder->startState('duplicate_test');
    }

    public function testGetWorkflowName()
    {
        $builder = new WorkflowBuilder('test');
        $this->assertEquals('test', $builder->getWorkflowName());
        $builder = new WorkflowBuilder(' ', 'null');
        $this->assertEquals(' ', $builder->getWorkflowName());
    }
}

<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\WorkflowManager;
use TyHand\WorkflowBundle\Workflow\Workflow;
use TyHand\WorkflowBundle\Workflow\State;

class WorkflowManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDefinitionAndGetWorkflow()
    {
        // Create a dummy definition
        $workflow = new Workflow('manager_test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $workflow->addState($stateA);
        $workflow->setInitialState($stateA);
        $mockBuilder = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock();
        $mockBuilder->expects($this->any())
            ->method('build')
            ->will($this->returnValue($workflow));
        $mockDefinition = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\AbstractWorkflowDefinition')
            ->setMethods(array('getName', 'getContextClass', 'build'))
            ->getMock();
        $mockDefinition->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('manager_test'));
        $mockDefinition->expects($this->any())
            ->method('getContextClass')
            ->will($this->returnValue('TyHand\WorkflowBundle\Tests\DummyContext'));
        $mockDefinition->expects($this->any())
            ->method('build')
            ->will($this->returnValue($mockBuilder));

        // Create the new manager
        $manager = new WorkflowManager();
        $this->assertCount(0, $manager->getDefinitions());

        $manager->addWorkflowDefinition($mockDefinition);
        $this->assertCount(1, $manager->getDefinitions());

        $retrieved = $manager->getWorkflow('manager_test');
        $this->assertNotNull($retrieved);
        $this->assertEquals($retrieved, $workflow);

        $retrieved2 = $manager->getWorkflow('manager_test');
        $this->assertNotNull($retrieved2);
        $this->assertEquals($retrieved2, $retrieved);
    }

    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\WorkflowNotFoundException
     * @expectedExceptionMessage Workflow with name "unicorn" was not found.  Names found are [ "manager_test" ]
     */
    public function testWorkflowNotFoundException()
    {
        // Create a dummy definition
        $workflow = new Workflow('manager_test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $workflow->addState($stateA);
        $workflow->setInitialState($stateA);
        $mockBuilder = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\Builder\WorkflowBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock();
        $mockBuilder->expects($this->any())
            ->method('build')
            ->will($this->returnValue($workflow));
        $mockDefinition = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\AbstractWorkflowDefinition')
            ->setMethods(array('getName', 'getContextClass', 'build'))
            ->getMock();
        $mockDefinition->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('manager_test'));
        $mockDefinition->expects($this->any())
            ->method('getContextClass')
            ->will($this->returnValue('TyHand\WorkflowBundle\Tests\DummyContext'));
        $mockDefinition->expects($this->any())
            ->method('build')
            ->will($this->returnValue($mockBuilder));

        // Create the new manager
        $manager = new WorkflowManager();
        $manager->addWorkflowDefinition($mockDefinition);

        // Get a non-existent workflow
        $workflow = $manager->getWorkflow('unicorn');
    }
}

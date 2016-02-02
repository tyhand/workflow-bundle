<?php

namespace TyHand\WorkflowBundle\Tests\Command;

use TyHand\WorkflowBundle\Command\WorkflowManagerDebugCommand;
use TyHand\WorkflowBundle\Workflow\WorkflowManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class WorkflowManagerDebugCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        // Lets create a workflow manager
        $workflowManager = new WorkflowManager();

        // Lets create a mock DI container
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $mockContainer->expects($this->any())
            ->method('get')
            ->with($this->equalTo('tyhand_workflow.manager'))
            ->will($this->returnValue($workflowManager));

        // Lets start the application
        $application = new Application();
        $commandObject = new WorkflowManagerDebugCommand();
        $commandObject->setContainer($mockContainer);
        $application->add($commandObject);

        $command = $application->find('tyhand_workflow:manager:debug');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        // Lets add some mock definitions to the workflow manager
        $mockDefinition = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\AbstractWorkflowDefinition')
            ->setMethods(array('getName', 'getContextClass', 'build'))
            ->getMock();
        $mockDefinition->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('fake'));
        $mockDefinition->expects($this->any())
            ->method('getContextClass')
            ->will($this->returnValue('TyHand\WorkflowBundle\Tests\DummyContext'));
        $mockDefinition->expects($this->any())
            ->method('build')
            ->will($this->returnValue(false));

        $workflowManager->addWorkflowDefinition($mockDefinition);

        $mockDefinition2 = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\AbstractWorkflowDefinition')
            ->setMethods(array('getName', 'getContextClass', 'build'))
            ->getMock();
        $mockDefinition2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        $mockDefinition2->expects($this->any())
            ->method('getContextClass')
            ->will($this->returnValue('TyHand\WorkflowBundle\Tests\DummyContext'));
        $mockDefinition2->expects($this->any())
            ->method('build')
            ->will($this->returnValue(false));

        $workflowManager->addWorkflowDefinition($mockDefinition2);

        // Test the command again
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/fake/', $commandTester->getDisplay());
        $this->assertRegExp('/abstract/', $commandTester->getDisplay());
        $this->assertRegExp('/TyHand\\\WorkflowBundle\\\Tests\\\DummyContext/', $commandTester->getDisplay());
        $this->assertRegExp('/Mock_AbstractWorkflowDefinition/', $commandTester->getDisplay());
    }
}

<?php

namespace TyHand\WorkflowBundle\Tests\Workflow\Context;

use TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity;

class ContextTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAddRemoveWorkflowInstance()
    {
        // create the mock for this trait
        $context = $this->getMockForTrait('\TyHand\WorkflowBundle\Workflow\Context\ContextTrait');

        // Test get with nothing in it
        $this->assertNotNull($context->getWorkflowInstances());
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $context->getWorkflowInstances());
        $this->assertCount(0, $context->getWorkflowInstances());

        // Add in a workflow instance
        $workflowInstance = new WorkflowInstanceEntity();
        $workflowInstance->setWorkflowName('test1');
        $context->addWorkflowInstance($workflowInstance);

        $this->assertCount(1, $context->getWorkflowInstances());
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity', $context->getWorkflowInstances()[0]);
        $this->assertEquals('test1', $context->getWorkflowInstances()[0]->getWorkflowName());

        $workflowInstance2 = new WorkflowInstanceEntity();
        $workflowInstance2->setWorkflowName('test2');
        $context->addWorkflowInstance($workflowInstance2);

        $this->assertCount(2, $context->getWorkflowInstances());
        $this->assertEquals('test1', $context->getWorkflowInstances()[0]->getWorkflowName());
        $this->assertEquals('test2', $context->getWorkflowInstances()[1]->getWorkflowName());

        // Test remove
        $context->removeWorkflowInstance($workflowInstance);
        $this->assertCount(1, $context->getWorkflowInstances());
        $this->assertEquals('test2', $context->getWorkflowInstances()[1]->getWorkflowName());
    }

    /**
     * @depends testGetAddRemoveWorkflowInstance
     */
    public function testGetWorkflowInstanceForWorkflow()
    {
        $context = $this->getMockForTrait('\TyHand\WorkflowBundle\Workflow\Context\ContextTrait');
        $workflowInstance = new WorkflowInstanceEntity();
        $workflowInstance->setWorkflowName('test1');
        $context->addWorkflowInstance($workflowInstance);
        $workflowInstance2 = new WorkflowInstanceEntity();
        $workflowInstance2->setWorkflowName('test2');
        $context->addWorkflowInstance($workflowInstance2);

        $this->assertCount(1, $context->getWorkflowInstancesForWorkflow('test1'));
        $this->assertCount(1, $context->getWorkflowInstancesForWorkflow('test2'));
        $this->assertCount(0, $context->getWorkflowInstancesForWorkflow('test3'));
        $this->assertEquals('test1', $context->getWorkflowInstancesForWorkflow('test1')->first()->getWorkflowName());
        $this->assertEquals('test2', $context->getWorkflowInstancesForWorkflow('test2')->first()->getWorkflowName());

        $workflowInstance3 = new WorkflowInstanceEntity();
        $workflowInstance3->setWorkflowName('test1');
        $workflowInstance3->setIsComplete(true);
        $context->addWorkflowInstance($workflowInstance3);

        $this->assertCount(2, $context->getWorkflowInstancesForWorkflow('test1'));
        $this->assertCount(1, $context->getWorkflowInstancesForWorkflow('test2'));
        $this->assertCount(1, $context->getWorkflowInstancesForWorkflow('test1', true));
        $this->assertFalse($context->getWorkflowInstancesForWorkflow('test1', true)->first()->isComplete());
    }

    /**
     * @depends testGetWorkflowInstanceForWorkflow
     */
    public function testHasWorkflowInstanceForWorkflow()
    {
        $context = $this->getMockForTrait('\TyHand\WorkflowBundle\Workflow\Context\ContextTrait');
        $workflowInstance = new WorkflowInstanceEntity();
        $workflowInstance->setWorkflowName('test1');
        $context->addWorkflowInstance($workflowInstance);
        $workflowInstance2 = new WorkflowInstanceEntity();
        $workflowInstance2->setWorkflowName('test2');
        $workflowInstance2->setIsComplete(true);
        $context->addWorkflowInstance($workflowInstance2);

        $this->assertTrue($context->hasWorkflowInstancesForWorkflow('test1'));
        $this->assertTrue($context->hasWorkflowInstancesForWorkflow('test2'));
        $this->assertTrue($context->hasWorkflowInstancesForWorkflow('test1', true));
        $this->assertFalse($context->hasWorkflowInstancesForWorkflow('test2', true));
        $this->assertFalse($context->hasWorkflowInstancesForWorkflow('test5'));
        $this->assertFalse($context->hasWorkflowInstancesForWorkflow('test5', true));
    }
}

<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\WorkflowNotFoundException;

class WorkflowNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new WorkflowNotFoundException('imaginary', array('real', 'irrational'));
        $this->assertEquals('Workflow with name "imaginary" was not found.  Names found are [ "real", "irrational" ]', $exception->getMessage());
        $this->assertEquals('imaginary', $exception->getRequestedWorkflowName());
        $this->assertCount(2, $exception->getKnownWorkflowNames());
        $this->assertContains('real', $exception->getKnownWorkflowNames());
        $this->assertContains('irrational', $exception->getKnownWorkflowNames());
    }
}

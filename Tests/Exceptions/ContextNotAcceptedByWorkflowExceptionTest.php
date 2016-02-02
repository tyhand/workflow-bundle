<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\ContextNotAcceptedByWorkflowException;

class ContextNotAcceptedByWorkflowExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new ContextNotAcceptedByWorkflowException('MyClass', 'Required');
        $this->assertEquals('Workflow expecting context of class "Required" but got a context of class "MyClass"', $exception->getMessage());
        $this->assertEquals('Required', $exception->getWorkflowContextClass());
        $this->assertEquals('MyClass', $exception->getContextClass());
    }
}

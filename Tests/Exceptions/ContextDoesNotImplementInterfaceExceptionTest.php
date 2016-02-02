<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\ContextDoesNotImplementInterfaceException;

class ContextDoesNotImplementInterfaceExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new ContextDoesNotImplementInterfaceException('MyClass', 'MyInterface');
        $this->assertEquals('Context class "MyClass" does not implement required interface "MyInterface"', $exception->getMessage());
        $this->assertEquals('MyClass', $exception->getContextClass());
        $this->assertEquals('MyInterface', $exception->getMissingInterface());
    }
}

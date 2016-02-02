<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\WorkflowNameAlreadyUsedException;

class WorkflowNameAlreadyUsedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new WorkflowNameAlreadyUsedException('double');
        $this->assertEquals('Workflow name "double" was already used.', $exception->getMessage());
        $this->assertEquals('double', $exception->getDuplicatedName());
    }
}

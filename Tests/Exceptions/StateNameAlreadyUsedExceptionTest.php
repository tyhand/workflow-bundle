<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\StateNameAlreadyUsedException;

class StateNameAlreadyUsedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new StateNameAlreadyUsedException('double');
        $this->assertEquals('State name "double" was already used in this workflow.', $exception->getMessage());
        $this->assertEquals('double', $exception->getDuplicatedName());
    }
}

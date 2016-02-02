<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\StateNotFoundException;

class StateNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new StateNotFoundException('imaginary', array('real', 'irrational'));
        $this->assertEquals('State with name "imaginary" was not found.  Names found are [ "real", "irrational" ]', $exception->getMessage());
        $this->assertEquals('imaginary', $exception->getRequestedStateName());
        $this->assertCount(2, $exception->getKnownStateNames());
        $this->assertContains('real', $exception->getKnownStateNames());
        $this->assertContains('irrational', $exception->getKnownStateNames());
    }
}

<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException;
use TyHand\WorkflowBundle\Workflow\State;

class StateDoesNotMatchNameExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $state = new State('A');
        $exception = new StateDoesNotMatchNameException($state, 'B');
        $this->assertEquals('Trying to set state with name "A" when object was expecting a state with name "B"', $exception->getMessage());
        $this->assertEquals($state, $exception->getState());
        $this->assertEquals('B', $exception->getSetName());
    }
}

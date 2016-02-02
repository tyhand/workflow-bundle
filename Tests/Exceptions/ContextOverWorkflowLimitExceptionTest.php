<?php

namespace TyHand\WorkflowBundle\Tests\Exceptions;

use TyHand\WorkflowBundle\Exceptions\ContextOverWorkflowLimitException;

class ContextOverWorkflowLimitExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new ContextOverWorkflowLimitException(5, 'abstract');
        $this->assertEquals('This workflow only allows a context to have 5 abstract instances', $exception->getMessage());
        $this->assertEquals(5, $exception->getLimit());
        $this->assertEquals('abstract', $exception->getLimitType());
    }
}

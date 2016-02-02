<?php

namespace TyHand\WorkflowBundle\Tests\Events;

use TyHand\WorkflowBundle\Events\WorkflowEvent;
use TyHand\WorkflowBundle\Tests\DummyContext;

class WorkflowEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        // Just testing that the event name exists and is a string
        $this->assertInternalType('string', WorkflowEvent::WORKFLOW_EVENT);
    }

    public function testGets()
    {
        $context = new DummyContext();
        $event = new WorkflowEvent($context, 'testflow', 'eventname');
        $this->assertEquals($context, $event->getContext());
        $this->assertEquals('testflow', $event->getWorkflowName());
        $this->assertEquals('eventname', $event->getEventName());
    }
}

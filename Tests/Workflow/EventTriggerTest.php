<?php

namespace TyHand\WorkflowBundle\Tests\Workflow;

use TyHand\WorkflowBundle\Workflow\EventTrigger;
use TyHand\WorkflowBundle\Workflow\State;

class EventTriggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException
     * @expectedExceptionMessage Trying to set state with name "jif" when object was expecting a state with name "gif"
     */
    public function testSetStateNameMismatchException()
    {
        $eventTrigger = new EventTrigger('pronunciation.correct', 'gif');
        $eventTrigger->setState(new State('jif'));
    }

    public function testGetSetState()
    {
        $eventTrigger = new EventTrigger('clown_invasion', 'defcon_1');
        $eventTrigger->setState(new State('defcon_1'));
        $this->assertInstanceOf('\TyHand\WorkflowBundle\Workflow\State', $eventTrigger->getState());
        $this->assertEquals('defcon_1', $eventTrigger->getState()->getName());
    }

    public function testGetStateName()
    {
        $eventTrigger = new EventTrigger('spider_invasion', 'doom');
        $this->assertEquals('doom', $eventTrigger->getStateName());
    }

    public function testGetEventName()
    {
        $eventTrigger = new EventTrigger('cookie_invasion', 'bliss');
        $this->assertEquals('cookie_invasion', $eventTrigger->getEventName());
    }

    public function testIsComplete()
    {
        $eventTrigger = new EventTrigger('go', 'stateTwo');
        $this->assertFalse($eventTrigger->isComplete());
        try {
            $eventTrigger->setState(new State('stateZero'));
        } catch (\TyHand\WorkflowBundle\Exceptions\StateDoesNotMatchNameException $e) {
            // just silencing the exception to test that the state is not set
        }
        $this->assertFalse($eventTrigger->isComplete());

        $eventTrigger->setState(new State('stateTwo'));
        $this->assertTrue($eventTrigger->isComplete());
    }
}

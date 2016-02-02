<?php

namespace TyHand\WorkflowBundle\Tests\Listener;

use TyHand\WorkflowBundle\Listeners\WorkflowEventListener;
use TyHand\WorkflowBundle\Events\WorkflowEvent;
use TyHand\WorkflowBundle\Workflow\Workflow;
use TyHand\WorkflowBundle\Workflow\State;
use TyHand\WorkflowBundle\Workflow\EventTrigger;
use TyHand\WorkflowBundle\Tests\DummyContext;

class WorkflowEventListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnWorkflowEvent()
    {
        // Create a test workflow
        $workflow = new Workflow('test', 'TyHand\WorkflowBundle\Tests\DummyContext');
        $stateA = new State('A');
        $stateB = new State('B');
        $stateC = new State('C');
        $triggerA1 = new EventTrigger('go', 'B');
        $triggerA1->setState($stateB);
        $triggerA2 = new EventTrigger('skip', 'C');
        $triggerA2->setState($stateC);
        $triggerB1 = new EventTrigger('go', 'C');
        $triggerB1->setState($stateC);
        $stateA->addEventTrigger($triggerA1);
        $stateA->addEventTrigger($triggerA2);
        $stateB->addEventTrigger($triggerB1);
        $workflow->addState($stateA);
        $workflow->addState($stateB);
        $workflow->addState($stateC);
        $workflow->setInitialState($stateA);

        // Create a mock workflow manager
        $mockManager = $this->getMockBuilder('TyHand\WorkflowBundle\Workflow\WorkflowManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWorkflow'))
            ->getMock();
        $mockManager->expects($this->any())
            ->method('getWorkflow')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($workflow));

        // create a mock object manager
        $mockObjectManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('flush'))
            ->getMock();
        $mockObjectManager->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(true));

        // create the listener
        $listener = new WorkflowEventListener($mockManager, $mockObjectManager);

        // Test 1 (A -> B -> C)
        $dummyContext = new DummyContext();
        $instance = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'go');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('B', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'go');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('C', $instance->getStateName());

        // Test 2 (A -> C)
        $dummyContext = new DummyContext();
        $instance = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'skip');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('C', $instance->getStateName());

        // Test 3 (A -> B -> C & A -> B)
        $dummyContext = new DummyContext();
        $instance = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'go');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('B', $instance->getStateName());

        $instance2 = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance2->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'go');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('C', $instance->getStateName());
        $this->assertEquals('B', $instance2->getStateName());

        // Test 4 (A -> B -> B)
        $dummyContext = new DummyContext();
        $instance = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'go');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('B', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'skip');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('B', $instance->getStateName());

        // Test 5 (A -> A)
        $dummyContext = new DummyContext();
        $instance = $workflow->start($dummyContext);
        $this->assertEquals('A', $instance->getStateName());

        $event = new WorkflowEvent($dummyContext, 'test', 'jump');
        $listener->onWorkflowEvent($event);

        $this->assertEquals('A', $instance->getStateName());
    }
}

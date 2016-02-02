# Workflow Bundle

A simple experimental Symfony bundle built to help manage some of our application workflows.  Don't know if I like the end result, but here it is.

## Requirements

Symfony 2.3 or higher and Doctrine 2.2 or higher.

## Installation

To install with composer, add the bundle to your composer json.
```bash
$ composer require tyhand/workflow-bundle
```

Next register the bundle with the AppKernel.
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Tyhand\WorkflowBundle\TyHandWorkflowBundle()
    ];
}
```

## Usage

### Making a workflow instance
To make an entity be able to be used as a workflow instance, you need to implement the context interface in the entity class.
```php

use TyHand\WorkflowBundle\Workflow\Context\ContextInterface;

class MyEntity implements ContextInterface
{
    //...
}
```

After that the methods in the interface will need to be implemented, or you can add the TyHand\WorkflowBundle\Workflow\Context\ContextTrait trait to the entity to implement all the methods in the interface.
With the interface added and the methods implemented or the trait added, run the doctrine schema commands to add the workflow tables.  Dumping the SQL should show tables for the workflow context and many-to-many tables for each entity that implements the context interface.  

```bash
$ app/console doctrine:schema:update --dump-sql
```

Once the dumped SQL is verified, update the schema either through a forced update or a database migration.

```bash
$ app/console doctrine:schema:update --force
```

### Creating a workflow
Start a new workflow by creating a new workflow definition class that extends the abstract workflow definition provided by the bundle.
```php
use TyHand\WorkflowBundle\Workflow\AbstractWorkflowDefinition;
use TyHand\WorkflowBundle\Workflow\Builder\WorkflowflowBuilder;

class MyWorkflow extends AbstractWorkflowDefinition
{
    //...
}
```

The abstract workflow definition has three methods that are abstract that will need to be implemented.  First is getName() which just returns a name for the workflow.  This name should be unique in the scope of the application.  The second method is getContextClass() which returns the name of the class that is the context of the workflow.

```php
// ...
class MyWorkflow extends AbstractWorkflowDefinition
{
    public function getName()
    {
        return 'my_workflow';
    }

    public function getContextClass()
    {
        return 'AppBundle\MyEntity';
    }

    // ...
}
```

The third required method is the build method which is the meat of the workflow definition.  This method will receive and return a workflow builder that specifies the structure of the workflow.

```php
\\...
class MyWorkflow extends AbstractWorkflowDefinition
{
    // ...

    public function build(WorkflowBuilder $builder)
    {
        return $builder
            ->activeLimit(1) // The number of times a single context can be active in the workflow
            ->totalLimit(1) // The maximum number of times a single context can go through a workflow
            ->initial('state_a') // The initial state
            // States follow here
            // ...
        ;
    }
}
```

The main part of the workflow are the states.  States each have a unique name (in the scope of the workflow), a set of exit conditions, and an optional set of actions.  If a state has no exit conditions then it is considered a terminal state, and once a workflow instance reaches this point, it is no longer considered active.  Actions are methods that can be called when an instance enters a workflow.  The exit conditions can either be an event that can be fired elsewhere in the application, a conditional statement that is evaluated upon an instance entering the state, and a time limit that moves to a given state if an instance has been in the state for so long.  Note: to use the time limit condition, a cron job or something similar will need to run the app/console check time limit command ('tyhand_workflow:states:check_time_limit') at set intervals or your choosing.

Example:
```php
public function build(WorkflowBuilder $builder)
{
    return $builder
        ->activeLimit(1) // The number of times a single context can be active in the workflow
        ->totalLimit(1) // The maximum number of times a single context can go through a workflow
        ->initial('state_a') // The initial state
        ->startState('state_a')
            ->addEvent('move_state', 'state_b') // EVENT EXIT (when move_state workflow event is thrown, move to state b)
        ->end()
        ->startState('state_b')
            ->startCondition() // CONDITIONAL EXIT
                ->conditionalFunction(function ($context) {
                    return 5 < $context->getBar();
                })
                ->ifTrue('state_c1') // GOTO state_c1 if bar < 5
                ->ifFalse('state_c2') // GOTO state_c2 if bar >= 5
            ->end()
        ->end()
        ->startState('state_c1')
            ->addAction(function ($context) { // ACTION
                $context->incrementFoo();
            })
            ->startCondition() // CONDITIONAL EXIT (with no ifFalse)
                ->conditionalFunction(function($context) {
                    return 10 === $context->getFoo();
                })
                ->ifTrue('state_d')
            ->end()
            ->startCondition() // CONDITIONS called in order
                ->conditionalFunction(function($context) {
                    return 2 < $context->getBar();
                })
                ->ifTrue('state_d')
            ->end()
            ->setTimeLimit(3600, 'state_d') // TIME LIMIT EXIT In 3600 second (1 hour) goto state d
        ->end()
        ->startState('state_c2')
            ->addAction(function ($context) { // ACTION
                $context->decrementFoo();
            })
            ->addEvent('move_state', 'state_d') // EVENT EXIT goto d if workflow event move state is thrown
        ->end()
        ->startState('state_d') // terminal state
        ->end()
    ;
}
```

To make state actions and conditions more useful, just inject various services into the workflow definition and add them to the conditions or action function.  For example, let's say we want to throw an event at the end of the workflow, then just add an action kind of like this to your terminal state.
```php
// WORKFLOW BUILDER METHOD
// ...
->startState('complete')
    ->addAction(function ($context) use ($eventDispatcher, $entityManager) {
        $eventDispatcher->dispatcher(
            MyObjectEvent::PROCESS_COMPLETE,
            new MyObjectEvent($entityManager, $context)
        );
    })
->end()
```

Make a the workflow manager aware of the definition class by making it a service and adding the 'tyhand_workflow.definition' tag.  Example:
```yaml
services:
    # ...
    my_workflow:
        tags:
            - { name: tyhand_workflow.definition }
```

### Placing instance into a workflow

Put a context into a workflow by using the workflow manager service.

```php
    // Create a new instance at the initial state
    $instance = $this->get('tyhand_workflow.manager')->getWorkflow('my_workflow')->start($myEntity);

    // Persist and flush the new instance entity
    $this->getDoctrine()->getManager()->persist($instance);
    $this->getDoctrine()->getManager()->flush($instance);
```

### Workflow events

Trigger the event transtions by throwing a workflow event with context, workflow name, and event name.
```php
    $this->get('event_dispatcher')->dispatch(
        \TyHand\WorkflowBundle\Event\WorkflowEvent::WORKFLOW_EVENT,
        new \TyHand\WorkflowBundle\Event\WorkflowEvent($myEntity, 'my_workflow', 'my_event')
    );
```

## Testing

PHPUnit is used for testing the bundle.  Create a phpunit.xml from the phpunit.xml.dist file located in the root of the bundle, and then run phpunit to test.

## License

This bundle is under the [MIT License](LICENSE)

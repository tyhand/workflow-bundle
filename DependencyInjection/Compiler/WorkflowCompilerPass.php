<?php

namespace TyHand\WorkflowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Takes the workflow definitions and adds them to the workflow manager service
 * Definitions need to extend the abstract definition class
 */
class WorkflowCompilerPass implements CompilerPassInterface
{
    ///////////////////////
    // INTERFACE METHODS //
    ///////////////////////

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Check for the workflow manager definition
        if ($container->hasDefinition('tyhand_workflow.manager')) {
            $workflowManagerDefinition = $container->getDefinition(
                'tyhand_workflow.manager'
            );
        } else {
            return; // Nothing to do here
        }

        // Get all the services tagged with the tyhand_workflow.definition
        $taggedServices = $container->findTaggedServiceIds(
            'tyhand_workflow.definition'
        );

        // Add each definition to the manager
        foreach($taggedServices as $id => $tags) {
            $workflowManagerDefinition->addMethodCall(
                'addWorkflowDefinition',
                array(new Reference($id))
            );
        }
    }
}

<?php

namespace TyHand\WorkflowBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use TyHand\WorkflowBundle\DependencyInjection\Compiler\WorkflowCompilerPass;

class TyHandWorkflowBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WorkflowCompilerPass());
    }
}

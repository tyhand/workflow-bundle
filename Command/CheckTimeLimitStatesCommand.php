<?php

namespace TyHand\WorkflowBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTimeLimitStatesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('tyhand_workflow:states:check_time_limit')
            ->setDescription('Checks the time limited states')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the states that need checked
        $checks = $this->getContainer()->get('tyhand_workflow.manager')->getTimeLimitedStateChecks();
        if (0 === count($checks)) {
            $output->writeln('There are no states with time limits');
            return;
        }

        // Get the list of instances that are due for changing
        $needUpdated = $this->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('TyHandWorkflowBundle:WorkflowInstanceEntity')
            ->getInstancesPastTimeLimit($checks);

        // Update
        foreach($needUpdated as $instance) {
            // Get the workflow
            $workflow = $this->getContainer()->get('tyhand_workflow.manager')->getWorkflow($instance->getWorkflowName());

            // Get the context through a bit of a silly loop around
            $queryBuilder = $this->getContainer()->get('doctrine.orm.entity_manager')
                ->getRepository($workflow->getContextClass())->createQueryBuilder('context');
            $queryBuilder->select('context')
                ->join('context.workflowInstances', 'workflowInstance')
                ->where('workflowInstance.id = :workflowInstanceId')
                ->setMaxResults(1)
                ->setParameter('workflowInstanceId', $instance->getId());
            $context = $queryBuilder->getQuery()->getOneOrNullResult();

            // Get the current state
            $state = $workflow->getState($instance->getStateName());

            $state->getTimeLimit()->getState()->moveTo($context, $instance);
        }

        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }
}

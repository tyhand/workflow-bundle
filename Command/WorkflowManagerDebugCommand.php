<?php

namespace TyHand\WorkflowBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

/**
 * Command to dump the contents of the workflow manager
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class WorkflowManagerDebugCommand extends ContainerAwareCommand
{
    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('tyhand_workflow:manager:debug')
            ->setDescription('Dumps the contents of the workflow manager')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the workflow manager
        $manager = $this->getContainer()->get('tyhand_workflow.manager');

        // Start the table
        $table = $this->getHelper('table');
        $table->setHeaders(array('Name', 'Context', 'Class'));
        $table->setLayout(TableHelper::LAYOUT_BORDERLESS);

        // Print out the known definitions
        foreach($manager->getDefinitions() as $definition) {
            // Place a row in the table
            $table->addRow(array(
                $definition->getName(),
                $definition->getContextClass(),
                get_class($definition)
            ));
        }

        // Render the table
        $table->render($output);
    }
}

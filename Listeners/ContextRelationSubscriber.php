<?php

namespace TyHand\WorkflowBundle\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Doctrine events subscriber that will listen for the load class metadata event
 * and
 *
 * @author Ty Hand <https://github.com/tyhand>
 */
class ContextRelationSubscriber implements EventSubscriber
{
    ///////////////
    // CONSTANTS //
    ///////////////

    /**
     * Constant value for the class name of the context interface
     *
     * @var string
     */
    const CONTEXT_INTERFACE = 'TyHand\WorkflowBundle\Workflow\Context\ContextInterface';

    /**
     * Constanct value for the class name of the workflow instance entity
     *
     * @var string
     */
    const INSTANCE_ENTITY = 'TyHand\WorkflowBundle\Entity\WorkflowInstanceEntity';

    ///////////////////////
    // INTERFACE METHODS //
    ///////////////////////

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata
        );
    }

    /**
     * Event action to add the mapping to any entity using the context interface
     *
     * @param  LoadClassMetadataEventArgs $eventArgs Doctrine event args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // Filter out metadata that is not using the context interface
        if (!in_array(self::CONTEXT_INTERFACE, class_implements($eventArgs->getClassMetadata()->getName()))) {
            return;
        }

        // Create a variable to the naming strategy for code clarity reasons
        $namingStrategy = $eventArgs->getEntityManager()->getConfiguration()->getNamingStrategy();

        // Create a many to many mapping between the context entity and the instance entity
        $eventArgs->getClassMetadata()->mapManyToMany(array(
            'targetEntity' => self::INSTANCE_ENTITY,
            'fieldName' => 'workflowInstances',
            'cascade' => array('persist'),
            'joinTable' => array(
                'name' => strtolower($namingStrategy->classToTableName($eventArgs->getClassMetadata()->getName())) . '__tyhand_workflow_instance',
                'joinColumns' => array(
                    array(
                        'name' => $namingStrategy->joinKeyColumnName($eventArgs->getClassMetadata()->getName()),
                        'referencedColumnName' => $namingStrategy->referenceColumnName(),
                        'onDelete' => 'CASCADE',
                        'onUpdate' => 'CASCADE'
                    )
                ),
                'inverseJoinColumns' => array(
                    array(
                        'name' => 'workflow_instance_id',
                        'referencedColumnName' => $namingStrategy->referenceColumnName(),
                        'onDelete' => 'CASCADE',
                        'onUpdate' => 'CASCADE'
                    )
                )
            )
        ));
    }
}

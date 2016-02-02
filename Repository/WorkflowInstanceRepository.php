<?php

namespace TyHand\WorkflowBundle\Repository;

use Doctrine\ORM\EntityRepository;

class WorkflowInstanceRepository extends EntityRepository
{
    /**
     * Get the instances in time limited states that are past the states
     * time limit
     *
     * @param  array  $timeLimitChecks Time limit check objects
     *
     * @return array Instances past their time limit
     */
    public function getInstancesPastTimeLimit(array $timeLimitChecks)
    {
        $queryBuilder = $this->createQueryBuilder('workflowInstance');
        $queryBuilder->select('workflowInstance');

        // And a where statment for each time limit check
        foreach($timeLimitChecks as $index => $check) {
            $queryBuilder
                ->orWhere('(
                    workflowInstance.workflowName = :workflowName' . $index . '
                    AND workflowInstance.stateName = :stateName' . $index . '
                    AND workflowInstance.stateDate < :earliestTime' . $index . '
                )')
                ->setParameter('workflowName' . $index, $check->getWorkflowName())
                ->setParameter('stateName' . $index, $check->getStateName())
                ->setParameter('earliestTime' . $index, $check->getEarliestStateTime())
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}

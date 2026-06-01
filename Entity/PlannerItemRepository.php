<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<PlannerItem>
 */
class PlannerItemRepository extends CommonRepository
{
    public function getTableAlias(): string
    {
        return 'pi';
    }

    public function findForMonth(int $year, int $month): array
    {
        $start = new \DateTime(sprintf('%d-%02d-01', $year, $month));
        $end   = new \DateTime($start->format('Y-m-t'));

        return $this->createQueryBuilder('pi')
            ->leftJoin('pi.assignedTo', 'u')
            ->addSelect('u')
            ->where('pi.deadline >= :start')
            ->andWhere('pi.deadline <= :end')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('pi.deadline', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findForYear(int $year): array
    {
        return $this->createQueryBuilder('pi')
            ->leftJoin('pi.assignedTo', 'u')
            ->addSelect('u')
            ->where('pi.deadline >= :start')
            ->andWhere('pi.deadline <= :end')
            ->setParameter('start', $year.'-01-01')
            ->setParameter('end', $year.'-12-31')
            ->orderBy('pi.deadline', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllItems(): array
    {
        return $this->createQueryBuilder('pi')
            ->leftJoin('pi.assignedTo', 'u')
            ->addSelect('u')
            ->orderBy('pi.deadline', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

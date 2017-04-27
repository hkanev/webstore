<?php

namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Discount;
use Doctrine\ORM\EntityManager;

class SortDiscountManager
{
    protected $manager;

    function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function findGeneralDiscounts()
    {
        $qb = $this->manager->getRepository(Discount::class)->createQueryBuilder('d');

        $today = new \DateTime();

        return $qb
            ->select('d')
            ->leftJoin('d.products', 'p')
            ->where($qb->expr()->lte('d.startDate', ':today'))
            ->andWhere($qb->expr()->gte('d.endDate', ':today'))
            ->andWhere($qb->expr()->isNull('p.discount'))
            ->andWhere($qb->expr()->isNull('d.category'))
            ->andWhere($qb->expr()->isNull('d.cash'))
            ->setParameter(':today', $today->format("Y-m-d"))
            ->orderBy('d.discount', 'DESC');
    }

    public function findCategoryDiscounts()
    {
       $qb = $this->manager->getRepository(Discount::class)->createQueryBuilder('d');

        $today = new \DateTime();

        return $qb
            ->select('d')
            ->where($qb->expr()->lte('d.startDate', ':today'))
            ->andWhere($qb->expr()->gte('d.endDate', ':today'))
            ->andWhere($qb->expr()->isNotNull('d.category'))
            ->andWhere($qb->expr()->isNull('d.cash'))
            ->setParameter(':today', $today->format("Y-m-d"))
            ->orderBy('d.discount', 'DESC');
    }

    public function findProductsDiscount()
    {
        $qb = $this->manager->getRepository(Discount::class)->createQueryBuilder('d');

        $today = new \DateTime();

        return $qb
            ->select('d')
            ->join('d.products', 'p')
            ->where($qb->expr()->lte('d.startDate', ':today'))
            ->andWhere($qb->expr()->gte('d.endDate', ':today'))
            ->andWhere($qb->expr()->isNotNull('p.discount'))
            ->andWhere($qb->expr()->isNull('d.category'))
            ->andWhere($qb->expr()->gt('d.cash', 0))
            ->setParameter(':today', $today->format("Y-m-d"))
            ->orderBy('d.discount', 'DESC');

    }
}
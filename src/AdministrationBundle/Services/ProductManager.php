<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/26/2017
 * Time: 6:08 PM
 */

namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Product;
use Doctrine\ORM\EntityManager;

class ProductManager
{
    protected $manager;

    function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function sortUserProducts($sort)
    {
        if($sort == null){
            $order[0] = 'p.createdOn';
            $order[1] = 'desc';
        } else {
            $order = explode("_", $sort);
            $order[0] = 'p.'.$order[0];
        }

        $qb = $this->manager->getRepository(Product::class)->createQueryBuilder('p');

        return $qb
            ->select('p')
            ->Where('p.onSale = 1')
            ->andWhere($qb->expr()->gt('p.quantity', 0))
            ->andWhere($qb->expr()->isNotNull('p.selelr'))
            ->orderBy($order[0], $order[1]);

    }

    public function sortProductsByCategory($sort, $seller, $category)
    {
       return $this->sortProducts($sort, $seller)
             ->andWhere('p.category = :cat')->setParameter('cat', $category);
    }

    public function sortProducts($sort)
    {
       return  $this->getBasicProductQuery($sort);
    }

    private function getBasicProductQuery($sort)
    {
        if($sort == null){
            $order[0] = 'p.createdOn';
            $order[1] = 'desc';
        } else {
            $order = explode("_", $sort);
            $order[0] = 'p.'.$order[0];
        }

        $qb = $this->manager->getRepository(Product::class)->createQueryBuilder('p');

        return $qb
            ->select('p')
            ->Where('p.onSale = 1')
            ->andWhere($qb->expr()->gt('p.quantity', 0))
            ->orderBy($order[0], $order[1]);

    }
}
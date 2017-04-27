<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/27/2017
 * Time: 5:19 PM
 */

namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Orders;
use AdministrationBundle\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\User;

class OrderManager
{
    private $tokenStorage;

    private $discountCalcultaor;

    /**
     * OrderManager constructor.
     * @param $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, DiscountCalculator $discountCalculator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->discountCalcultaor = $discountCalculator;
    }


    public function setTotalPrice($orders)
    {
        $totalPrice = 0;
        foreach ($orders as $order) {
            if($order->getProduct()->getSelelr() == null){
                $price = $this->discountCalcultaor->calculate($order->getProduct());
            } else {
                $price = $order->getProduct()->getPrice();
            }
            $quantity = $order->getProductQuantity();
            $totalPrice += $price * $quantity;
        }
        return $totalPrice;
    }

    public function setUserCash(Orders $order)
    {
        /** @var Product $product */
      $product = $order->getProduct();

      /** @var User $seller */
      $seller = $product->getSelelr();

       if($seller == null){
           return null;
       }

       $totalCash = ($seller->getCash() + $product->getPrice()*$order->getProductQuantity());
       $seller->setCash($totalCash);

       return $seller;
    }

    public function manageProduct(Orders $order)
    {

        /** @var Product $product */
        $product = $order->getProduct();

        $quantity = $product->getQuantity() - $order->getProductQuantity();

        if($quantity < 0){
            return null;
        }

        $product->setSold($product->getSold() + $order->getProductQuantity());
        $product->setQuantity($quantity);

        return $product;
    }

    public function manageCash($totalPrice)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $cash = $user->getCash() - $totalPrice;
        if ($cash < 0) {
          return null;
        }
        $user->setCash($cash);

        return $user;
    }
}
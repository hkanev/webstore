<?php


namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Category;
use AdministrationBundle\Repository\DiscountRepository;
use AdministrationBundle\Repository\PromotionRepository;

class DiscountManager
{
    protected $general_discount;

    protected $category_discount;

    protected  $products_discount;

    protected $user_cash_discount;


    function __construct(DiscountRepository $repository)
    {
        $this->general_discount = $repository->fetchBiggestGeneralDiscount();
        $this->category_discount = $repository->fetchCategoriesDiscount();
        $this->products_discount = $repository->fetchProductsDiscount();
        $this->user_cash_discount = $repository->fetchUserCashPromotion();
    }

    public function getGeneralDiscount()
    {
        return $this->general_discount ?? 0;
    }

    public function getCategoryDiscount($category)
    {
        return $this->category_discount[$category->getId()];
    }

    public function getProductsDiscount($product)
    {
        return $this->products_discount[$product->getId()];
    }

    public function getUserCashDiscount($user)
    {
        return $this->user_cash_discount[$user->getId()];
    }

    public function hasCategoryDiscount($category)
    {
        return array_key_exists($category->getId(), $this->category_discount);
    }

    public function hasProductsDiscount($product)
    {
        return array_key_exists($product->getId(), $this->products_discount);
    }

    public function hasUserCashDiscount($user)
    {
        return array_key_exists($user->getId(), $this->user_cash_discount);
    }
}
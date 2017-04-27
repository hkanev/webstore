<?php
namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Discount;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DiscountCalculator
{
    /**
     * @var DiscountManager
     */
    protected $manager;

    protected $tokenStorage;

    private $user;

    /**
     * DiscountCalculator constructor.
     * @param EntityManager $emanager
     */
    public function __construct(DiscountManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->user = $this->tokenStorage->getToken()->getUser();
    }

    public function calculate($product)
    {
        $category = $product->getCategory();

        $promotion = $this->manager->getGeneralDiscount();
        if ($this->manager->hasCategoryDiscount($category)) {
            $currentPromotion = $this->manager->getCategoryDiscount($category);
            if ($currentPromotion > $promotion) {
                $promotion = $currentPromotion;
            }
        }

        if ($this->manager->hasProductsDiscount($product)) {
            $currentPromotion = $this->manager->getProductsDiscount($product);
            if ($currentPromotion > $promotion) {
                $promotion = $currentPromotion;
            }
        }
        if($this->user != 'anon.') {
            if ($this->manager->hasUserCashDiscount($this->user)) {
                $currentPromotion = $this->manager->getUserCashDiscount($this->user);
                if ($currentPromotion > $promotion) {
                    $promotion = $currentPromotion;
                }
            }
        }
        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}
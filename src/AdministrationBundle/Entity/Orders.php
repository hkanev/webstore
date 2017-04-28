<?php

namespace AdministrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AdministrationBundle\Repository\OrdersRepository")
 */
class Orders
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="productQuantity", type="integer", nullable=true)
     */
    private $productQuantity;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="order")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AdministrationBundle\Entity\Product", inversedBy="order")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="AdministrationBundle\Entity\Checkout", inversedBy="orders")
     */
    private $checkout;

    /**
     * @var boolean
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted;

    /**
     * @var int
     * @ORM\Column(name="sell_quantity", type="integer", nullable=true)
     */
    private $sellQuantity;

    function __construct()
    {
        $this->deleted = false;
        $this->sellQuantity = 0;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Cart constructor.
     */

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;
    }

    /**
     * Get productQuantity
     *
     * @return int
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * @param mixed $checkout
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function getSellQuantity(): int
    {
        return $this->sellQuantity;
    }

    /**
     * @param int $sellQuantity
     */
    public function setSellQuantity(int $sellQuantity)
    {
        $this->sellQuantity = $sellQuantity;
    }


}


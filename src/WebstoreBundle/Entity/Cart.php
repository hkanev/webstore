<?php

namespace WebstoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="WebstoreBundle\Repository\CartRepository")
 */
class Cart
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
     @
     * @ORM\Column(name="productQuantity", type="integer", nullable=true)
     */
    private $productQuantity;

    /**
     * @ORM\OneToOne(targetEntity="WebstoreBundle\Entity\User", inversedBy="cart")
     */
    private $user;

    /**
     * @ManyToMany(targetEntity="WebstoreBundle\Entity\Product")
     ** @JoinTable(name="cart_product",
     *      joinColumns={@JoinColumn(name="cart_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="product_id", referencedColumnName="id")}
     *      )
     */
    private $products;


    /**
     * @ORM\OneToOne(targetEntity="WebstoreBundle\Entity\Orders", mappedBy="cart")
     */
    private $order;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->user = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     *
     * @return Cart
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
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
    public function getProducts()
    {
        return $this->products;
    }

    /**
     *
     * @return (Product|integer)[]
     */
    public function findProducts()
    {
        $products = [];

        foreach($this->products as $product)  {

            $products[] = $product->getId();
        }

        return $products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }
}


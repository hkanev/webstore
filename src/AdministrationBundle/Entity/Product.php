<?php

namespace AdministrationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AdministrationBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     * @Assert\Range(min="0.99")
     * @Assert\NotNull()
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var integer
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\Range(min="0")
     * @Assert\NotNull()
     */
    private $quantity;

    /**
     * @var integer
     * @ORM\Column(name="sold", type="integer", nullable=true)
     */
    private $sold;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_on", type="datetimetz", nullable=false)
     */
    private $createdOn;

    /**
     * @var boolean
     * @ORM\Column(name="on_sale", type="boolean", nullable=false)
     */
    private $onSale;

    /**
     * @ORM\ManyToOne(targetEntity="AdministrationBundle\Entity\Discount", inversedBy="products")
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="products")
     */
    private $selelr;

    /**
     * @ORM\Column(type="string")
     * @Assert\Image(mimeTypes="image/*", maxSize="5M")
     */
    private $image;

    /**
     * @var Review[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AdministrationBundle\Entity\Review", mappedBy="product")
     */
    private $reviews;

    /**
     * @ORM\ManyToOne(targetEntity="AdministrationBundle\Entity\Category", inversedBy="products")
     * @Assert\NotNull()
     */
    private $category;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AdministrationBundle\Entity\Orders", mappedBy="product")
     */
     private $order;

    function __construct()
    {
        $this->createdOn = new \DateTime();
        $this->onSale = true;
        $this->reviews = new ArrayCollection();
        $this->order = new ArrayCollection();
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param ArrayCollection|Review[] $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getSold()
    {
        return $this->sold;
    }

    /**
     * @param int $sold
     */
    public function setSold($sold)
    {
        $this->sold = $sold;
    }

    /**
     * @return bool
     */
    public function isOnSale()
    {
        return $this->onSale;
    }

    /**
     * @param bool $onSale
     */
    public function setOnSale($onSale)
    {
        $this->onSale = $onSale;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param ArrayCollection $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getSelelr()
    {
        return $this->selelr;
    }

    /**
     * @param mixed $selelr
     */
    public function setSelelr($selelr)
    {
        $this->selelr = $selelr;
    }



    function __toString()
    {
        return $this->name;
    }
}


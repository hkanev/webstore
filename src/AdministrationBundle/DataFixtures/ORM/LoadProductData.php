<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/20/2017
 * Time: 3:53 PM
 */

namespace AdministrationBundle\DataFixtures\ORM;


use AdministrationBundle\Entity\Category;
use AdministrationBundle\Entity\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\Role;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $category = $this->getReference('category-Nokia');
        $category1 = $this->getReference('category-LG');
        $category2 = $this->getReference('category-HTC');

        $this->buildProduct($manager, $category, 'Nokia',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'Samsung',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'LG',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'Apple',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC1',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC2',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC3',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC4',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category1, 'HTC5',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'Nokia12',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'Samsung13',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'LG14',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'Apple15',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'HTC16',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC17',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC228',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC38',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC48',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC58',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC161',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC171',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC281',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC381',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category2, 'HTC481',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
        $this->buildProduct($manager, $category, 'HTC581',999.99, 50, 'Awesome nokia gsm with brand new touch screen.', 'gsm.png');
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 4;
    }

    private function buildProduct(ObjectManager $manager, $category, $name, $price, $quantity, $description, $imageName)
    {
        $product = new Product();
        $product->setName($name);
        $product->setImage($imageName);
        $product->setCategory($category);
        $product->setPrice($price);
        $product->setQuantity($quantity);
        $product->setDescription($description);

        $manager->persist($product);
        $manager->flush();

        $this->addReference('product-'.$name, $product);
    }
}
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
        $nokia = $this->getReference('category-Nokia');
        $lg = $this->getReference('category-LG');
        $htc = $this->getReference('category-HTC');
        $samsung = $this->getReference('category-Samsung');
        $apple = $this->getReference('category-Apple');



        $this->buildProduct($manager, $samsung, 'Galaxy S7', 1200, 50, 'samsung.jpg', self::description);
        $this->buildProduct($manager, $nokia, 'Lumia 920', 685, 50, 'nokia.jpg', self::description);
        $this->buildProduct($manager, $lg, 'G6', 1600, 50, 'lg.jpg', self::description);
        $this->buildProduct($manager, $htc, 'U', 380, 50, 'sony.jpg', self::description);
        $this->buildProduct($manager, $apple, 'Iphone 7', 1790, 50, 'apple.jpg', self::description);

        $this->buildProduct($manager, $samsung, 'Galaxy S6', 1100, 50, 'samsung1.jpg', self::description);
        $this->buildProduct($manager, $nokia, 'Lumia 820', 585, 50, 'nokia1.jpg', self::description);
        $this->buildProduct($manager, $lg, 'G5', 1500, 50, 'lg1.jpg', self::description);
        $this->buildProduct($manager, $htc, 'U2', 680, 50, 'sony1.jpg', self::description);
        $this->buildProduct($manager, $apple, 'Iphone 6', 1290, 50, 'apple1.jpg', self::description);

        $this->buildProduct($manager, $samsung, 'Galaxy S5', 400, 50, 'samsung2.jpg', self::description);
        $this->buildProduct($manager, $nokia, 'Lumia 320', 185, 50, 'nokia2.jpg', self::description);
        $this->buildProduct($manager, $lg, 'G1', 500, 50, 'lg2.jpg', self::description);
        $this->buildProduct($manager, $htc, 'One X', 980, 50, 'sony2.jpg', self::description);
        $this->buildProduct($manager, $apple, 'Iphone 2', 390, 50, 'apple2.jpg', self::description);
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

    private function buildProduct(ObjectManager $manager, $category, $name, $price, $quantity, $imageName,$description)
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

    CONST description = "<table>
	<tr>
		<th>Year:</th>
		<td>2016</td>
	</tr>
	<tr>
		<th>Display:</th>
		<td>5.5 inch, 1440 x 2560 pixels</td>
	</tr>
	<tr>
		<th>Camera:</th>
		<td>12MP</td>
	</tr>
	<tr>
		<th>Front Camera:</th>
		<td>32MP</td>
	</tr>
	<tr>
		<th>Size:</th>
		<td>150.9 x 72.6 x 7.7 mm </td>
	</tr>
	<tr>
		<th>Weight:</th>
		<td>170g</td>
	</tr>
	<tr>
		<th>Capacity:</th>
		<td>32/64 GB</td>
	</tr>
	<tr>
		<th>Card:</th>
		<td>microSD, up to 200 GB (dedicated slot)</td>
	</tr>
	<tr>
		<th>OS:</th>
		<td>Android OS, v6.0 (Marshmallow)</td>
	</tr>
	<tr>
		<th>RAM:</th>
		<td>4 GB RAM</td>
	</tr>
	<tr>
		<th>CPU:</th>
		<td>Octa-core (4x2.45 GHz Kryo & 4x1.9 GHz Kryo)</td>
	</tr>
	<tr>
		<th>Wi-Fi:</th>
		<td>802.11b/g/n/ac</td>
	</tr>
	<tr>
		<th>NFC:</th>
		<td>Yes</td>
	</tr>
	<tr>
		<th>GPS:</th>
		<td>Yes</td>
	</tr>
	<tr>
		<th>Battery:</th>
		<td>Non-removable Li-Ion 3600 mAh</td>
	</tr>
</table>";
}


<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/20/2017
 * Time: 3:53 PM
 */

namespace AdministrationBundle\DataFixtures\ORM;


use AdministrationBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\Role;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->buildCategory($manager, 'Nokia', 'cnokia.png');
        $this->buildCategory($manager, 'Apple', 'capple.png');
        $this->buildCategory($manager, 'LG', 'clg.png');
        $this->buildCategory($manager, 'HTC', 'chtc.png');
        $this->buildCategory($manager, 'Samsung', 'csamsung.png');
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    private function buildCategory(ObjectManager $manager, $name, $imageName)
    {
        $category = new Category();
        $category->setName($name);
        $category->setImage($imageName);

        $manager->persist($category);
        $manager->flush();

        $this->addReference('category-'.$name, $category);
    }
}
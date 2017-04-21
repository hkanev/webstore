<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/20/2017
 * Time: 3:53 PM
 */

namespace UserBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Role;

class LoadRoleDate extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $role = new Role();
        $role->setName('ROLE_USER');

        $roleAdmin = new Role();
        $roleAdmin->setName('ROLE_ADMIN');

        $roleEditor = new Role();
        $roleEditor->setName('ROLE_EDITOR');

        $manager->persist($role);
        $manager->persist($roleAdmin);
        $manager->persist($roleEditor);
        $manager->flush();

        $this->addReference('role-user', $role);
        $this->addReference('role-admin', $roleAdmin);
        $this->addReference('role-editor', $roleEditor);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
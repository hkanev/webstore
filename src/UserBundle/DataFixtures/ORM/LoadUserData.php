<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/20/2017
 * Time: 3:26 PM
 */

namespace UserBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $rolesAdmin = [$this->getReference('role-admin'), $this->getReference('role-user'), $this->getReference('role-editor')];
        $rolesEditor = [$this->getReference('role-user'), $this->getReference('role-editor')];
        $roleUser = [$this->getReference('role-user')];

        $this->buildUser($manager, $rolesAdmin, 'admin@email.com', '123', 'admin', 'adminov', 'adminLand', '023456789', 'varna');
        $this->buildUser($manager, $rolesEditor, 'editor@email.com', '123', 'Editor', 'Editov', 'editLand', '023456789', 'varna');

        for($i = 1; $i <= 10; $i++){
            $this->buildUser($manager, $roleUser, 'user'.$i.'@email.com', '123', 'User'.$i, 'UserLastName', 'None', '1234566612', 'Sofia');
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function buildUser(ObjectManager $manager, $roles, $email, $password, $firstName, $secondName, $address, $telephone, $city)
    {
        $user = new User();

        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, $password);

        $user->setFirstName($firstName);
        $user->setSecondName($secondName);
        $user->setEmail($email);
        $user->setAddress($address);
        $user->setTelephone($telephone);
        $user->setCity($city);
        $user->setPassword($password);

        foreach ($roles as $role){
            $user->addRole($role);
        }
        $manager->persist($user);
        $manager->flush();
    }
}
<?php

namespace AdministrationBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdministrationBundle\Entity\User;
use AdministrationBundle\Form\UserType;


/**
 * Class AdminController
 * @package WebstoreBundle\Controller
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_show")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = count($em->getRepository('WebstoreBundle:Category')->findAll());
        $products = count($em->getRepository('WebstoreBundle:Product')->findAll());
        $users = count($em->getRepository('WebstoreBundle:User')->findAll());

        return $this->render('admin/index.html.twig', array(
            'categories' => $categories, 'products' => $products, 'users' => $users
        ));
    }

    /**
     * @Route("/admin/users/", name="users_list")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_EDITOR')")
     */
    public function listUserAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/list_user.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/admin/users/{id}", name="users_view")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserAction(User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        return $this->render('admin/view_user.html.twig', ['form' => $form->createView() , 'user' => $user]);
    }

    /**
     * @Route("/admin/users/delete/{id}", name="user_delete")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $this->addFlash("info", "User ". $user->getUsername(). " deleted!");
        return $this->redirectToRoute("users_list");
    }
}

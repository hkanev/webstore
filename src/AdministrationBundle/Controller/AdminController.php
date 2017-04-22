<?php

namespace AdministrationBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;


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

        $categories = ($em->getRepository('AdministrationBundle:Category')->findAll());
        $products = $em->getRepository('AdministrationBundle:Product')->findProducts();
        $users = ($em->getRepository('UserBundle:User')->findAll());

        return $this->render('@Administration/admin/index.html.twig', array(
            'categories' => $categories, 'products' => $products, 'users' => $users
        ));
    }

    /**
     * @Route("/admin/users/", name="users_list")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_EDITOR')")
     */
    public function listUserAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
            ->select('u')->orderBy('u.cash', 'asc');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1),
            12
        );
        return $this->render('@Administration/admin/list_user.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/admin/users/{id}", name="users_view")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserAction(User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        return $this->render('@Administration/admin/view_user.html.twig', ['form' => $form->createView() , 'user' => $user]);
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

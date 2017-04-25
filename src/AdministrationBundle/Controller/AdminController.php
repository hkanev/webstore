<?php

namespace AdministrationBundle\Controller;


use AdministrationBundle\Form\AdminUserEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Role;
use UserBundle\Entity\User;
use UserBundle\Form\ChangePassword;
use UserBundle\Form\UserType;


/**
 * Class AdminController
 * @package WebstoreBundle\Controller
 * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
 *
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
     * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
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
     * @Route("/admin/users/edit/{id}", name="admin_user_edit")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminEditUser(User $user, Request $request)
    {
        $form = $this->createForm(AdminUserEditType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("info", "User ". $user->getUsername(). " edited!");
            return $this->redirectToRoute('users_list');
        }
        return $this->render('@User/user/register.html.twig', array('user' => $user,
            'form' => $form->createView()));
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
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/users/{id}/change_password", name="admin_change_password")
     */
    public function changePassword(User $user, Request $request)
    {
        $form = $this->createForm(ChangePassword::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())    {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("info", "User ". $user->getUsername(). " edited!");
            return $this->redirectToRoute('users_view', ['id' => $user->getId()]);
        }

        return $this->render('@User/user/register.html.twig', array('user' => $user,
            'form' => $form->createView()));
    }
}

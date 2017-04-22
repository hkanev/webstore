<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UserBundle\Entity\Role;
use UserBundle\Entity\User;
use UserBundle\Form\ChangePassword;
use UserBundle\Form\UserEditType;
use UserBundle\Form\UserType;

class UserController extends Controller
{
    const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) Find role
            $roleRepo = $this->getDoctrine()->getRepository(Role::class);
            $role = $roleRepo->findOneBy(['name' => self::ROLE_DEFAULT]);

            $user->addRole($role);

            if($user->getPassword() == null){

            }

            // 5) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash("info", "User ". $user->getUsername(). " created!");
            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            '@User/user/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("profile/edit", name="user_edit")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $user->getRoles();

        $form = $this->createForm(UserEditType::class, $user);

        $roleRepo = $this->getDoctrine()->getRepository(Role::class);
        $roles = $roleRepo->findAll();

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())    {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("info", "User ". $user->getUsername(). " edited!");
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('@User/user/register.html.twig', array('user' => $user,
            'form' => $form->createView()));
    }

    /**
    * @Route("profile/change_password", name="user_change_password")
    * @param Request $request
    * @return Response
    */
    public function changePassword(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePassword::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())    {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("info", "User ". $user->getUsername(). " edited!");
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('@User/user/register.html.twig', array('user' => $user,
            'form' => $form->createView()));
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return $this->render("@User/user/profile.html.twig", ['user'=>$user]);
    }
}

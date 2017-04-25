<?php

namespace AdministrationBundle\Controller;

use AdministrationBundle\Entity\Category;
use AdministrationBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
 * @Route("category")
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     *
     * @Route("/", name="category_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository(Category::class)->createQueryBuilder('c')
            ->select('c')->where('c.deleted = 0')->orderBy('c.name', 'desc');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1),
            12
        );
        return $this->render('@Administration/category/index.html.twig',['pagination' => $pagination] );
    }

    /**
     * Creates a new category entity.
     *
     * @Route("/new", name="category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $category->getImage();
            $imageName = $this->get('app.image_uploader')->upload($image);
            $category->setImage($imageName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("info", "Category ". $category->getName(). " created!");
            return $this->redirectToRoute('category_index');
        }

        return $this->render('@Administration/category/new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $oldImage = $category->getImage();
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $image = $category->getImage();
            if($image == null){
               $image = new File($this->getParameter('images').$oldImage);
                $imageName = $this->get('app.image_uploader')->upload($image);
            } else {
                $imageName = $this->get('app.image_uploader')->upload($image);
            }
            $category->setImage($imageName);
            $this->getDoctrine()->getManager()->persist($category);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("info", "Category ". $category->getName(). " edited!");
            return $this->redirectToRoute('category_index');
        }

        return $this->render('@Administration/category/edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a category entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("POST")
     */
    public function deleteAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $category->setDeleted(1);
        $em->persist($category);
        $em->flush();
        $this->addFlash("info", "Category ". $category->getName(). " deleted!");
        return $this->redirectToRoute("category_index");
    }
}

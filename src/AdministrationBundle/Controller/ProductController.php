<?php

namespace AdministrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AdministrationBundle\Entity\Category;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Form\ProductType;

/**
 * Class ProductController
 * @package WebstoreBundle\Controller
 * @Security("has_role('ROLE_ADMIN', 'ROLE_EDITOR')")
 */
class ProductController extends Controller
{
    /**
     * @Route("/products", name="products_list")
     */
    public function listAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy([], ['price' => 'desc', 'name' => 'asc      ']);
        return $this->render('@Administration/products/list.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/products/add", name="product_add_form")
     * @Method("GET")
     */
    public function createAction()
    {
        $form = $this->createForm(ProductType::class);
        return $this->render('@Administration/products/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/products/add", name="product_add_process")
     * @Method("POST")
     */
    public function createProcessAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isValid()){

            $image = $product->getImage();
            $imageName = $this->get('app.image_uploader')->upload($image);
            $product->setImage($imageName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("info", "Product with name ". $product->getName(). " was added successfully!");

            return $this->redirectToRoute("products_list");
        }
        return $this->render('@Administration/products/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/products/edit/{id}", name="product_edit_form")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function editAction(Product $product)
    {
        $form = $this->createForm(ProductType::class, $product);

        return $this->render('@Administration/products/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/products/edit/{id}", name="product_edit_process")
     * @Method("POST")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editProcessAction(Product $product, Request $request)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("info", "Product with name ". $product->getName(). " was edited successfully!");
            return $this->redirectToRoute("products_list");
        }
        return $this->render('@Administration/products/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/products/delte/{id}", name="product_delete_process")
     * @Method("POST")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $this->addFlash("info", "Product ". $product->getName(). " deleted!");
        return $this->redirectToRoute("products_list");
    }
}

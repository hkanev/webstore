<?php

namespace AdministrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Form\DiscountType;

class DiscountController extends Controller
{
    /**
     * @Route("/discount/add/product/{id}", name="discount_product_add_form")
     * @Method("GET")
     */
    public function createProductDiscount(Product $product)
    {
        $form = $this->createForm(DiscountType::class, $product);
        return $this->render('discount/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("discount/add/product/{id}", name="discount_product_add_process")
     * @Method("POST")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createProductDiscountProcess(Product $product, Request $request)
    {
        $form = $this->createForm(DiscountType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("info", "Product with name ". $product->getName(). " was edited successfully!");
            return $this->redirectToRoute("webstore_index");
        }
        return $this->render('discount/create.html.twig', ['form' => $form->createView()]);
    }

}

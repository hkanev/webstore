<?php

namespace AdministrationBundle\Controller;

use AdministrationBundle\Entity\Discount;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Discount controller.
 * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
 * @Route("discount")
 */
class DiscountController extends Controller
{
    CONST productsPerPage = 12;
    /**
     * Lists all discount entities.
     *
     * @Route("/", name="discount_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $calc = $this->get('discount_calculator');
        $discounts =  $this->getDoctrine()->getRepository(Discount::class)->findDiscounts();
        return $this->render('discount/index.html.twig', ['pagination' => $discounts, 'calc' => $calc]);
    }

    /**
     * Creates a new discount entity.
     *
     * @Route("/general/new", name="create_general_discount")
     * @Method({"GET", "POST"})
     */
    public function createGeneralDiscount(Request $request)
    {
        $discount = new Discount();
        $form = $this->createForm('AdministrationBundle\Form\DiscountType', $discount);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            return $this->redirectToRoute('discount_index', array('id' => $discount->getId()));
        }

        return $this->render('discount/new.html.twig', array(
            'discount' => $discount,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new discount entity.
     *
     * @Route("/product/new", name="create_product_discount")
     * @Method({"GET", "POST"})
     */
    public function createProductDiscount(Request $request)
    {
        $discount = new Discount();
        $form = $this->createForm('AdministrationBundle\Form\DiscountType', $discount)
        ->add('products');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $form->get('products')->getNormData();

            foreach( $products as $product){
                $product->setDiscount($discount);
            }
            $discount->setProducts($products);

            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/new.html.twig', array(
            'discount' => $discount,
            'form' => $form->createView(),
        ));
    }


    /**
     * Creates a new discount entity.
     *
     * @Route("/category/new", name="create_category_discount")
     * @Method({"GET", "POST"})
     */
    public function createCategoryPromotion(Request $request)
    {
        $discount = new Discount();
        $form = $this->createForm('AdministrationBundle\Form\DiscountType', $discount)
            ->add('category');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            return $this->redirectToRoute('discount_show', array('id' => $discount->getId()));
        }

        return $this->render('discount/new.html.twig', array(
            'discount' => $discount,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new discount entity.
     *
     * @Route("/cash/new", name="create_cash_discount")
     * @Method({"GET", "POST"})
     */
    public function createCashDiscount(Request $request)
    {
        $discount = new Discount();
        $form = $this->createForm('AdministrationBundle\Form\DiscountType', $discount)
        ->add('cash');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            return $this->redirectToRoute('discount_index', array('id' => $discount->getId()));
        }

        return $this->render('discount/new.html.twig', array(
            'discount' => $discount,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing discount entity.
     *
     * @Route("/{id}/edit", name="discount_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Discount $discount)
    {
        $editForm = $this->createForm('AdministrationBundle\Form\DiscountType', $discount)
        ->add('cash')->add('category')->add('products');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $products = $editForm->get('products')->getNormData();

            foreach( $products as $product){
                $product->setDiscount($discount);
            }
            $discount->setProducts($products);

            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            return $this->redirectToRoute('discount_index', array('id' => $discount->getId()));
        }

        return $this->render('discount/edit.html.twig', array(
            'discount' => $discount,
            'edit_form' => $editForm->createView(),
        ));
    }
}

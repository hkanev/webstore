<?php

namespace AdministrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Entity\Review;
use AdministrationBundle\Form\ReviewType;

class ReviewController extends Controller
{
    /**
     * @Route("/products/{id}/reviews", name="product_reviews")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Product $product)
    {
        return $this->render('reviews/products.html.twig', ['product' => $product ]);
    }

    /**
     * @Route("/products/{id}/reviews/add", name="review_add_form")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("GET")
     */
    public function createaAction(Product $product)
    {
        return $this->render('reviews/leave_review.html.twig',
            ['form' => $this->createForm(ReviewType::class)->createView(), 'product' => $product]);
    }

    /**
     * @Route("/products/{id}/reviews/add", name="review_add_process")
     * @Method("POST")
     */
    public function createaProcessAction(Product $product, Request $request)
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        if($form->isValid()) {
            $review->setProduct($product);
            $product->getReviews()->add($review);
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();
            $this->addFlash("info", "Review added");
         return  $this->redirectToRoute('product_reviews', ['id' => $product->getId()]);
        }
        return $this->render('reviews/products.html.twig', ['product' => $product ]);
    }
}

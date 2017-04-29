<?php

namespace ShopBundle\Controller;

use AdministrationBundle\Entity\Category;
use AdministrationBundle\Entity\Discount;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends Controller
{
    CONST seller = 'FoxMobile';
    CONST productsPerPage = 12;


    /**
     * @Route("products/navigation", name="shop_navigation")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function navigationAction()
    {
        $categories = $this->getDoctrine()->getManager()->getRepository(Category::class)->findCategories();
        $topSellers =  $this->getDoctrine()->getManager()->getRepository(Product::class)->findTopSellers();;
        return $this->render('@Shop/Shop/navigation.html.twig', ['categories' => $categories, 'topSellers' => $topSellers]);
    }

    /**
     * @Route("/", name="shop_products")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productsShow(Request $request)
    {
        $calc = $this->get('discount_calculator');
        $query = $this->get('sort.products.manager')->sortProducts($request->get('option'));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1), self::productsPerPage
        );
        return $this->render('@Shop/Shop/product_list.html.twig', ['pagination' => $pagination, 'calc' => $calc]);
    }

    /**
     * @Route("/products/category/{id}", name="products_category_list")
     */
    public function productByCategory(Category $category, Request $request)
    {
        $calc = $this->get('discount_calculator');
        $query = $this->get('sort.products.manager')->sortProductsByCategory($request->get('option'), self::seller, $category);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1), self::productsPerPage
        );
        return $this->render('@Shop/Shop/product_list.html.twig', ['pagination' => $pagination, 'calc' => $calc]);
    }

    /**
     * @Route("/products_user_list", name="user_products_list")
     */
    public function userProducts( Request $request)
    {
        $calc = $this->get('discount_calculator');
        $query = $this->get('sort.products.manager')->sortUserProducts($request->get('option'));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1), self::productsPerPage
        );
        return $this->render('@Shop/Shop/user_product_list.html.twig', ['pagination' => $pagination, 'calc' => $calc]);
    }

    /**
     * @Route("/products/view/{id}", name="product_view")
     * @param Product $product
     * @return Response
     */
    public function productView(Product $product)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(ProductType::class, $product);

        return $this->render('@Shop/Shop/product.html.twig',
            ['form' => $form->createView() , 'product' => $product , 'categories' => $categories]);
    }
}

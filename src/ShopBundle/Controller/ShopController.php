<?php

namespace ShopBundle\Controller;

use AdministrationBundle\Entity\Category;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends Controller
{
    /**
     * @Route("/", name="shop_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $productRepo = $this->getDoctrine()->getRepository(Product::class);
        $promotion = $productRepo->findPromotion();
        $topSeller = $productRepo->findTopSellers();
        $recent = $productRepo->findRecentProducts();

        return $this->render('ShopBundle:Shop:index.html.twig', ['promotion' => $promotion, 'topSellers' => $topSeller, 'recent' => $recent]);
    }

    /**
     * @Route("products/navigation", name="shop_navigation")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function navigationAction()
    {
        $categories = $this->getDoctrine()->getManager()->getRepository(Category::class)->findAll();
        $topSellers =  $this->getDoctrine()->getManager()->getRepository(Product::class)->findTopSellers();;
        return $this->render('@Shop/Shop/navigation.html.twig', ['categories' => $categories, 'topSellers' => $topSellers]);
    }

    /**
     * @Route("/list", name="products_shop_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productsShow(Request $request)
    {
        $query = $this->buildSortableQuery($request->get('option'))->where('p.onSale = 1');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1),
            12
        );
        return $this->render('@Shop/Shop/product_list.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/products/category/{id}", name="products_category_list")
     */
    public function productByCategory(Category $category, Request $request)
    {
        $query = $this->buildSortableQuery($request->get('option'))->where('p.onSale = 1');
        $query->andWhere('p.category = :cat')->setParameter('cat', $category);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1),
            12
        );
        return $this->render('@Shop/Shop/product_list.html.twig', ['pagination' => $pagination]);
    }


    /**
     * @Route("/products/view/{id}", name="product_view")
     * @param Product $product
     * @return Response
     */
    public function viewAction(Product $product)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(ProductType::class, $product);

        return $this->render('@Shop/Shop/product.html.twig', ['form' => $form->createView() , 'product' => $product , 'categories' => $categories]);
    }


    private function buildSortableQuery($sort)
    {
        switch($sort) {
            case 'price_asc':
                return $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                    ->select('p')->orderBy('p.price', 'desc');
            case 'price_desc':
                return   $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                    ->select('p')->orderBy('p.price', 'asc');
            case 'recent':
                return $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                    ->select('p')->orderBy('p.createdOn', 'desc');
            case 'top_sellers':
                return $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                    ->select('p')->orderBy('p.sold', 'desc');
            default:
               return $query = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                    ->select('p')->orderBy('p.createdOn', 'desc');
        }
    }
}

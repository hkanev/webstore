<?php

namespace WebstoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Cart;
use WebstoreBundle\Entity\Orders;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Form\CartType;

class OrderController extends Controller
{
    /**
     * @Route("/order/add/{id}", name="add_to_cart")
     */
   public function addToCart(Product $product, Request $request)
   {
       $user = $this->getUser();
       if (!$user->getCart()) {
           $cart = new Cart();
           $cart->setUser($user);
       } else {
           $cart = $user->getCart();
       }
       foreach ($cart->findProducts() as $id) {
           if ($id == $product->getId()) {
               return $this->redirectToRoute('webstore_index');
           }
       }
           $cart->getProducts()->add($product);
           $cart->setProductQuantity($request->query->get('productQuantity'));
           $em = $this->getDoctrine()->getManager();
           $em->persist($cart);
           $em->flush();

           return $this->redirectToRoute('webstore_index');

   }

    /**
     * @Route("/cart", name="cart_show")
     */
   public function showCart()
   {
       $user = $this->getUser()->getId();
       $cart = $this->getDoctrine()->getManager()->getRepository(Cart::class)->findOneBy(['user' => $user]);

       return $this->render('order/my_cart.html.twig', ['cart' => $cart]);
   }

    /**
     * @Route("/order", name="cart_order")
     */
   public function order(Request $request)
   {
       $order = new Orders();
       $user = $this->getUser()->getId();
       $cart = $this->getDoctrine()->getManager()->getRepository(Cart::class)->findOneBy(['user' => $user]);
       $order->setCart($cart);

        dump($request);
        exit;
   }
}

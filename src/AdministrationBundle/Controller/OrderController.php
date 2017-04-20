<?php

namespace AdministrationBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AdministrationBundle\Entity\Cart;
use AdministrationBundle\Entity\Checkout;
use AdministrationBundle\Entity\Orders;
use AdministrationBundle\Entity\Product;
use AdministrationBundle\Form\CartType;

class OrderController extends Controller
{
    /**
     * @Route("/order/add/{id}", name="add_to_cart")
     */
   public function addToCart(Product $product, Request $request)
   {
       $user = $this->getUser();
       $order = new Orders();
       $order->setUser($user);
       $order->setProduct($product);
       $order->setProductQuantity($request->query->get('productQuantity'));

       $em = $this->getDoctrine()->getManager();
       $em->persist($order);
       $em->flush();

       return $this->redirectToRoute('webstore_index');
   }

    /**
     * @Route("/cart", name="cart_show")
     */
   public function showCart()
   {
      $orders = $this->getOrders();
      $totalPrice = $this->setTotalPrice($orders);

      return $this->render('order/my_cart.html.twig', ['orders' => $orders, 'totalprice' => $totalPrice]);
   }

    /**
     * @Route("/order", name="cart_order")
     */
   public function order(Request $request)
   {
       $user = $this->getUser();

       $checkout = new Checkout();
       $orders = $this->getOrders();
       $totalPrice = $this->setTotalPrice($orders);

       foreach($orders as $order){
          $order->setCheckout($checkout);
       }

       $cash = $user->getCash()-$totalPrice;
       if($cash < 0){
           throw new \Exception("Not enough money");
       }
       $user->setCash();
       $checkout->setTotalPrice($totalPrice);

       $em = $this->getDoctrine()->getManager();
       $em->persist($checkout);
       $em->persist($user);
       $em->flush();

       return $this->redirectToRoute('webstore_index');
   }

   private function getOrders()
   {
       $user = $this->getUser()->getId();
       return $orders = $this->getDoctrine()->getManager()->getRepository(Orders::class)->findBy(['user' => $user]);
   }

   private function setTotalPrice($orders)
   {
       $totalPrice = 0;
       foreach ($orders as $order) {
           $price = $order->getProduct()->getPrice();
           $quantity = $order->getProductQuantity();
           $totalPrice += $price * $quantity;
       }
       return $totalPrice;
   }
}

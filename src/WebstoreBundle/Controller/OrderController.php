<?php

namespace WebstoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Cart;
use WebstoreBundle\Entity\Product;

class OrderController extends Controller
{
    /**
     * @Route("/order/add/{id}", name="add_to_cart")
     */
   public function addToCart(Product $product)
   {
       $user = $this->getUser();
        if(!$user->getCart()){
            $cart = new Cart();
            $cart->setUser($user);
        } else {
            $cart = $user->getCart();
        }


       foreach ($cart->findProducts() as $id){
          if($id == $product->getId()){
             return $this->redirectToRoute('webstore_index');
          }
       }

       $cart->getProducts()->add($product);
       $cart->setProductQuantity(3);

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
        dump($cart);
       return $this->render('order/my_cart.html.twig', ['cart' => $cart]);
   }
}

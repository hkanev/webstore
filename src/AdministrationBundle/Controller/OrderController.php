<?php

namespace AdministrationBundle\Controller;

use AdministrationBundle\Entity\Category;
use AdministrationBundle\Form\CheckoutType;
use AdministrationBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AdministrationBundle\Entity\Checkout;
use AdministrationBundle\Entity\Orders;
use AdministrationBundle\Entity\Product;

class OrderController extends Controller
{
    /**
     * @Route("/order/add/{id}", name="add_to_cart")
     */
    public function addToCart(Product $product, Request $request)
    {
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('security_login');
        }

        $quantity =  $request->get('quantity');
        if($request->get('quantity') == null){
            $quantity = 1;
        }

        if($quantity > $product->getQuantity() ||  $quantity < 0){
            $this->addFlash('warning', "Invalid quantity   ");
            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        $order = $this->getDoctrine()->getRepository(Orders::class)->findExistingOrder($product, $user);
        if($order == null){
            $order = new Orders();
            $order->setUser($user);
            $order->setProduct($product);
            $order->setProductQuantity($quantity);
        } else {
            $order = $order[0];
            $order->setProductQuantity($order->getProductQuantity() + $quantity);
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('shop_products');
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function showCart()
    {
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('security_login');
        }


        $form = $this->createForm(CheckoutType::class, null, ['action' => $this->generateUrl('checkout')]);
        $calc = $this->get('discount_calculator');

        $orders = $this->getDoctrine()->getRepository(Orders::class)
                ->findOrders($this->getUser());
        $totalPrice = $this->get('order.manager')->setTotalPrice($orders);

        return $this->render('@Administration/order/my_cart.html.twig', ['orders' => $orders, 'totalprice' => $totalPrice,
           'calculator' => $calc, 'form' => $form->createView()]);
    }

    /**
     * @Route("/order/remove/{id}", name="order_remove_action")
     */
    public function removeFromCart(Orders $orders)
    {
        $orders->setDeleted(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($orders);
        $em->flush();

        return $this->redirectToRoute('cart_show');
    }


    /**
     *@Route("/profile/sell/{id}", name="user_sell_product")
     */
   public function sellBoughtProductProcess(Orders $order, Request $request)
   {
       if($order->getUser() != $this->getUser()){
           $this->addFlash('warning', 'Invalid user');
           return $this->redirectToRoute('shop_products');
       }

       if( $order->getProductQuantity() <= $order->getSellQuantity()){
           $this->addFlash('warning', 'Not enough quantity of '.$order->getProduct()->getName());
           return $this->redirectToRoute('user_profile');
       }


       $category = $this->getDoctrine()->getRepository(Category::class)
           ->findCategory('User');

       $product = $order->getProduct();
       $oldImage = $product->getImage();
       $product->setQuantity($order->getProductQuantity());
       $form = $this->createForm(ProductType::class, $product);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
            $image = $product->getImage();

           $newProduct = $this->get('user.product.manager')
                       ->createUserProduct($order, $image, $oldImage, $category, $this->getUser());

           if($order->getProductQuantity() < $newProduct->getQuantity() ){
               $this->addFlash('warning', 'Not enough quantity of '.$order->getProduct()->getName());
               return $this->redirectToRoute('user_profile');
           }
           $order->setSellQuantity($product->getQuantity());



           $em = $this->getDoctrine()->getManager();
           $em->refresh($product);
           $em->persist($newProduct);
           $em->flush();

           $this->addFlash("info", "Product with name ". $newProduct->getName(). " was created successfully!");
           return $this->redirectToRoute("user_profile");
       }

       return $this->render('AdministrationBundle:products:create.html.twig', ['form' => $form->createView()]);
   }
}

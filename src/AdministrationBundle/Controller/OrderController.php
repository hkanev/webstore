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
        $quantity =  $request->get('quantity');
        if($request->get('quantity') == null){
            $quantity = 1;
        }

        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('security_login');
        }
        $order = $this->getDoctrine()->getRepository(Orders::class)->findExistingOrder($product, $user);
            dump($order);
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

        return $this->redirectToRoute('products_shop_list');
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function showCart()
    {
        $form = $this->createForm(CheckoutType::class, null, ['action' => $this->generateUrl('checkout')]);
        $calc = $this->get('discount_calculator');

        if ($this->getUser() == null) {
            return $this->redirectToRoute('security_login');
        }

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
     * @Route("/profile/sell/{id}", name="user_sell_product_form")
     * @Method("GET")
     */
    public function sellBoughtProduct(Orders $order)
    {
        $product = $order->getProduct();
        $product->setQuantity($order->getProductQuantity());
        $form = $this->createForm(ProductType::class, $product);
        return $this->render('@Administration/products/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     *@Route("/profile/sell/{id}", name="user_sell_product_process")
     * @Method("POST")
     */
   public function sellBoughtProductProcess(Orders $order, Request $request)
   {
       $category = $this->getDoctrine()->getRepository(Category::class)
           ->findCategory('User');

       $product = $order->getProduct();
       $oldImage = $product->getImage();
       $form = $this->createForm(ProductType::class, $product);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
            $image = $product->getImage();

           $newProduct = $this->get('user.product.manager')
                       ->createUserProduct($order, $image, $oldImage, $category, $this->getUser());


           $em = $this->getDoctrine()->getManager();
           $em->refresh($product);
           $em->persist($newProduct);
           $em->flush();

           $this->addFlash("info", "Product with name ". $newProduct->getName(). " was created successfully!");
           return $this->redirectToRoute("products_list");
       }

       return $this->render('@Administration/products/create.html.twig', ['form' => $form->createView()]);
   }
}

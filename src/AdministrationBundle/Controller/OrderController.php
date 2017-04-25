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
        $order = $this->getDoctrine()->getRepository(Orders::class)->findOneBy(['product' => $product, 'user' => $user]);
        if($order == null){
            $order = new Orders();
            $order->setUser($user);
            $order->setProduct($product);
            $order->setProductQuantity($quantity);
        } else {
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
        $form = $this->createForm(CheckoutType::class, null, ['action' => $this->generateUrl('cart_order')]);
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('security_login');
        }

        $orderRepo = $this->getDoctrine()->getRepository(Orders::class);
        $orders = $orderRepo->findOrders($user);
        $totalPrice = $this->setTotalPrice($orders);

        return $this->render('@Administration/order/my_cart.html.twig', ['orders' => $orders, 'totalprice' => $totalPrice,
            'form' => $form->createView()]);
    }



    /**
     * @Route("/order", name="cart_order")
     */
    public function order(Request $request)
    {
        $user = $this->getUser();
        $orderRepo = $this->getDoctrine()->getRepository(Orders::class);
        $orders = $orderRepo->findOrders($user);
        $totalPrice = $this->setTotalPrice($orders);
        $checkout = new Checkout();
        foreach ($orders as $order) {
            $order->setCheckout($checkout);
            $product = $order->getProduct();
            $quantity = $product->getQuantity() - $order->getProductQuantity();

            if($quantity < 0){
                throw new \Exception("Invalid quantity");
            }
            $product->setSold($order->getProductQuantity());
            $product->setQuantity($quantity);
        }

        $cash = $user->getCash() - $totalPrice;
        if ($cash < 0) {
            throw new \Exception("Not enough money");
        }
        $user->setCash($cash);
        $checkout->setTotalPrice($totalPrice);

        $em = $this->getDoctrine()->getManager();
        $em->persist($checkout);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('products_shop_list');
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
       $category = $this->getDoctrine()->getRepository(Category::class)->find(7);
       $product = $order->getProduct();
       $newProduct = new Product();
       $oldImage = $product->getImage();
       $form = $this->createForm(ProductType::class, $product);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
           $image = $product->getImage();
           if($image == null){
               $image = new File($this->getParameter('images').$oldImage);
               $imageName = $this->get('app.image_uploader')->upload($image);
           } else {
               $imageName = $this->get('app.image_uploader')->upload($image);
           }
           $newProduct->setImage($imageName);
           $newProduct->setQuantity($product->getQuantity());
           $newProduct->setSeller($order->getUser()->getEmail());
           $newProduct->setCategory($category);
           $newProduct->setCreatedOn($product->getCreatedOn());
           $newProduct->setDescription($product->getDescription());
           $newProduct->setName($product->getName());
           $newProduct->setPrice($product->getPrice());

           $em = $this->getDoctrine()->getManager();
           $em->refresh($product);
           $em->persist($newProduct);
           $em->flush();

           $this->addFlash("info", "Product with name ". $product->getName(). " was edited successfully!");
           return $this->redirectToRoute("products_list");
       }
       return $this->render('@Administration/products/create.html.twig', ['form' => $form->createView()]);
   }
}

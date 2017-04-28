<?php

namespace AdministrationBundle\Controller;

use AdministrationBundle\Entity\Checkout;
use AdministrationBundle\Entity\Orders;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends Controller
{
    CONST status = 'In progress';
    CONST completeStatus = 'Complete';

    /**
     * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
     * @Route("/checkouts", name="list_checkouts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(    )
    {

        $checkouts = $this->getDoctrine()->getRepository(Checkout::class)->findBy(['status' => self::status]);

        return $this->render('@Administration/checkout/list.html.twig', ['checkouts' => $checkouts]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
     *  @Route("/checkouts/status/{id}", name="change_status")
     */
    public function changeStatus(Checkout $checkout)
    {
        $checkout->setStatus(self::completeStatus);
        $em = $this->getDoctrine()->getManager();
        $em->persist($checkout);
        $em->flush();

        return $this->redirectToRoute('list_checkouts');
    }

    /**
     * @Security("has_role('ROLE_ADMIN') | has_role('ROLE_EDITOR')")
     * @Route("/checkout/{id}", name="view_checkout")
     */
    public function viewCheckout(Checkout $checkout)
    {
        return $this->render('@Administration/checkout/checkout.html.twig', ['checkout' => $checkout]);
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(Request $request)
    {
        $checkout = new Checkout();
        $em = $this->getDoctrine()->getManager();
        $orders = $this->getDoctrine()->getRepository(Orders::class)
            ->findOrders($this->getUser());

        $totalPrice = $this->get('order.manager')->setTotalPrice($orders);

        foreach ($orders as $order) {
            $order->setCheckout($checkout);

            $seller = $this->get('order.manager')->setUserCash($order);
            if($seller != null){
                $em->persist($seller);
            }

            $product = $this->get('order.manager')->manageProduct($order);
            if($product == null){
                $this->addFlash('info', 'Invalid quantity');
                return $this->redirectToRoute('cart_show');
            }

            $em->persist($product);
        }


        $buyer = $this->get('order.manager')->manageCash($totalPrice);
        if($buyer == null){
            $this->addFlash('info', 'Not enough cash');
            return $this->redirectToRoute('cart_show');
        }


        $checkout->setTotalPrice($totalPrice);

        $em->persist($checkout);
        $em->persist($buyer);
        $em->flush();

        $this->addFlash('info', 'Successful purchase');
        return $this->redirectToRoute('products_shop_list');
    }
}

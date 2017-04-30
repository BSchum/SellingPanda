<?php

namespace ProductBundle\Controller;

use ProductBundle\Entity\orderproduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OrderController extends Controller
{
    /**
     * @Route("/cart/order")
     */
    public function OrderFormAction(){
        return $this->render('ProductBundle:ProductController:order_form.html.twig', array(
        ));
    }
    /**
     * @Route("/cart/ordered")
     */
    public function OrderedAction(){
        $em = $this->getDoctrine()->getManager();
        $order = new orderproduct();
        $order ->setName($_POST['name']);
        $order ->setSurname($_POST['surname']);
        $order ->setAddress($_POST['address']);
        $order->setDate(new \DateTime("now"));
        $cart = unserialize($_COOKIE["cart"]);
        $total = 0;
        $realCart = array();
        $qte = array();
        $repository = $em->getRepository("ProductBundle:product");
        foreach($cart as $productId){
            $product = $repository->find($productId);
            if(in_array($product,$realCart)){
                $index = array_search($product,$realCart);
                $qte[$index] = $qte[$index] +1;
            }
            else{
                array_push($realCart,$product);
                array_push($qte,1);
            }
            $total = $total + $product->getPrice();
        }
        $order->setQte($qte);
        $order->setTotalPrice($total);
        $order->setCart($realCart);
        $em->persist($order);
        $em->flush();
        return $this->render('ProductBundle:ProductController:ordered.html.twig', array(
        ));
    }
    /**
     * @Route("/admin/orderList")
     */
    public function OrderListAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:orderproduct");
        $orders = $repository->findAll();
        return $this->render('ProductBundle:ProductController:order_list.html.twig', array(
            'orders' => $orders
        ));
    }

}

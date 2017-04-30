<?php

namespace ProductBundle\Controller;

use ProductBundle\Entity\product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProductControllerController extends Controller
{
    /**
     * @Route("/")
     */
    public function ProductsListingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $products = $repository->findAll();

        return $this->render('ProductBundle:ProductController:products_listing.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/products/{category}")
     */
    public function ProductsByCategoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $products = $repository->findBy(array('category'=>$category));
        return $this->render('ProductBundle:ProductController:products_listing.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/product/{name}")
     */
    public function SingleProductAction($name)
    {
        $name = str_replace('_', ' ', $name);
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $product = $repository->findBy(array('name'=>$name));
        return $this->render('ProductBundle:ProductController:single_product.html.twig', array(
            'product' => $product
        ));
    }
    /**
     * @Route("/cart/add/{id}")
     */
    public function AddToCartAction($id)
    {

        $cart = array();
        if(isset($_COOKIE['cart']))
        {
            $cart = unserialize($_COOKIE['cart']);
        }
        else
        {
            $cart = array();
        }
        for($i=0;$i<$_POST['howmuch'];$i++) {
            array_push($cart, $id);
        }
        setcookie('cart',serialize($cart),time()+86000,"/");
        return $this->redirect('/cart');
    }

    /**
     * @Route("/cart")
     */
    public function CartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $cart = array();
        $qte = array();
        if(isset($_COOKIE['cart'])){
            $idInCart = unserialize($_COOKIE['cart']);

            foreach($idInCart as $id){
                $product = $repository->find($id);
                if(in_array($product,$cart)){
                    $index = array_search($product,$cart);
                    $qte[$index] = $qte[$index] +1;
                }
                else{
                    array_push($cart,$product);
                    array_push($qte,1);
                }
            }
        }

        return $this->render('ProductBundle:ProductController:cart.html.twig', array(
            'cart' => $cart,
            'qte' => $qte
        ));
    }
    /**
     * @Route("/category")
     */
    public function CategoryAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $qr = $repository ->createQueryBuilder("p")
                                ->select("DISTINCT(p.category)");

        $category = $qr->getQuery()->getResult();
        return $this->render('ProductBundle:ProductController:category.html.twig', array(
            'categorys' => $category
        ));
    }
    /**
     * @Route("/cart/delete/{id}")
     */
    public function DeleteFromCartAction($id)
    {
        $cart = unserialize($_COOKIE['cart']);
        for($i=0;$i<$_POST['deleteNumber'];$i++){
            $pos = array_search($id, $cart);
            if(in_array($id, $cart)){
                array_splice($cart,$pos,1);
            }
        }
        setcookie('cart',serialize($cart),time()+86000,"/");

        return $this->redirect('/cart');
    }

    /**
     * @Route("/admin/addForm")
     */
    public function AddFormAction(){
        return $this->render('ProductBundle:ProductController:add_product_form.html.twig', array(
        ));
    }
    /**
     * @Route("/admin/addProduct")
     */
    public function AddProductAction(){
        $em = $this->getDoctrine()->getManager();
        $product = new product();
        $product ->setName($_POST['productName']);
        $product ->setPrice($_POST['price']);
        $product ->setDescription($_POST['description']);
        $product->setCategory($_POST['category']);
        $product->setStock($_POST['stock']);
        $product->setImg($_POST['img']);
        $em->persist($product);
        $em->flush();
        return $this->render('ProductBundle:ProductController:add_product.html.twig', array(
        ));
    }
    /**
     * @Route("/admin/deleteForm")
     */
    public function DeleteFormAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $products = $repository->findAll();

        return $this->render('ProductBundle:ProductController:delete_product_form.html.twig', array(
            'products' => $products
        ));
    }
    /**
    * @Route("/admin/deleteProduct")
    */
    public function DeleteProductAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        foreach($_POST as $key=>$value)
        {
            $product = $repository->find($key);
            $em->remove($product);
        }
        $em->flush();
        return $this->render('ProductBundle:ProductController:delete_product.html.twig', array(

        ));
    }
    /**
     * @Route("/admin/modifyForm")
     */
    public function modifyFormAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $products = $repository->findAll();

        return $this->render('ProductBundle:ProductController:modify_product_form.html.twig', array(
            'products' => $products
        ));
    }
    /**
     * @Route("/admin/modifyProduct")
     */
    public function ModifyProductAction(){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $products = array();
        foreach($_POST as $key=>$value)
        {
            array_push($products,$repository->find($key));
        }
        return $this->render('ProductBundle:ProductController:modify_product.html.twig', array(
            'products' => $products
        ));
    }
    /**
     * @Route("/admin/modifiedProduct")
     */
    public function ModifiedProductAction(){

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ProductBundle:product");
        $product = $repository->find($_POST['productID']);
        $product ->setName($_POST['productName']);
        $product ->setPrice($_POST['price']);
        $product ->setDescription($_POST['description']);
        $product->setCategory($_POST['category']);
        $product->setStock($_POST['stock']);
        $product->setImg($_POST['img']);
        $em->persist($product);
        $em->flush();
        return $this->render('ProductBundle:ProductController:product_changed.html.twig', array(
        ));

    }

    /**
     * @Route("/admin")
     */
    public function adminPanelAction(){
        return $this->render('ProductBundle:ProductController:admin_panel.html.twig', array(

        ));
    }
    /**
     * @Route("/destroyCookie")
     */
    public function unsetCookieAction(){
        if (isset($_COOKIE['cart'])) {
            unset($_COOKIE['cart']);
            setcookie('cart', '', time() - 3600, '/'); // empty value and old timestamp
        }
        return $this->redirect( '/logout' );
    }


}

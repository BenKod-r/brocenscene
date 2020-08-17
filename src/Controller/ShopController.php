<?php
/**
 * Created by IntelliJ IDEA.
 * User: Benharrat Khaled
 * Date: 15/08/2020
 * Time: 12:37
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShopController
 * @package App\Controller
 * @Route("/shop")
 */
class ShopController extends AbstractController
{
    /**
     * @Route("/",name="shop_index")
     * @return Response
     */
    public function shop() :Response
    {
        return $this->render('shop/shop.html.twig');
    }
}
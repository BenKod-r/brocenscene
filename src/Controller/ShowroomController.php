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
 * Class ShowroomController
 * @package App\Controller
 * @Route("/showroom")
 */
class ShowroomController extends AbstractController
{
    /**
     * @Route("/",name="showroom_index")
     * @return Response
     */
    public function showroom() :Response
    {
        return $this->render('showroom/showroom.html.twig');
    }
}
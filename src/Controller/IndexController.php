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

class IndexController extends AbstractController
{
    /**
     * Home page display
     * Return the coach members
     * @Route("/",name="home_index")
     * @return Response A response instance
     */
    public function index() :Response
    {
        $response = file_get_contents('https://www.instagram.com/brocenscene/?__a=1');
        $data = json_decode($response, true);
        $followers = ($data["graphql"]["user"]["edge_followed_by"]);
        return $this->render('index.html.twig', [
            "followers" => $followers,
        ]);
    }

    /**
     * @Route("/showroom",name="showroom_index")
     * @return Response
     */
    public function showroom() :Response
    {
        return $this->render('showroom.html.twig');
    }
}
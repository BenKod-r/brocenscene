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
 * Class MentionsController
 * @package App\Controller
 * @Route("/mentions-legales")
 */
class MentionsController extends AbstractController
{
    /**
     * @Route("", name="mentions-legales")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('mentions-legales.html.twig');
    }
}
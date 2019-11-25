<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController {
    /**
     * @return Response
     * @Route("/", name="/", methods={"GET", "POST"})
     */
    public function index() {
//      zobrazení indexu
        return $this->render('pages/index.html.twig');
    }

}
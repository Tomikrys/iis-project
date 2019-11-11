<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class DefaultController extends AbstractController {
    /**
     * @return Response
     * @Route("/", methods={"GET", "POST"})
     */
    public function index() {
        return $this->render('pages/index.html.twig');
    }

}
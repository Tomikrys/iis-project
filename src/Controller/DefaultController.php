<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
    /**
     * @Route("/")
     * @Method({"GET", "POST"})
     */
    public function index() {
        return $this->render('pages/index.html.twig');
    }

}
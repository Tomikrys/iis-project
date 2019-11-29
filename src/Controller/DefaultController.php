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

    /**
     * @Route("/bring_me_back", name="/bring_me_back", methods={"GET", "POST", "DELETE"})
     */
    public function back() {
        return new Response(
            "<html>
            <body>
                <script>
                window.history.go(-2);
                </script>
            </body>
        </html>");
    }

}
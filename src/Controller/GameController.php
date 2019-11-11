<?php


namespace App\Controller;


use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController {


    /**
     * @param Request $request
     * @Route("/game/update_score/{id}", methods={"PATCH"})
     * @return Response
     */
    public function update_game_score(Request $request, $id) {
        $json = file_get_contents('php://input');

        $data = json_decode ($json);

        $entityManager = $this->getDoctrine()->getManager();
        $game = $entityManager->getRepository(Game::class)->find($id);

        if (!$game) {
            throw $this->createNotFoundException(
                'Nenalezena hra pro id '.$id
            );
        }

        $game->setPointsTeam1($data->team1);
        $game->setPointsTeam2($data->team2);
        $entityManager->flush();

        $response = new Response();
        $response->send();
        return $response;
    }
}
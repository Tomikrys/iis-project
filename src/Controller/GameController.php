<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// controller pro správe jednotlivých her turnaje
class GameController extends AbstractController {


    /**
     * @param Request $request
     * @Route("/game/update_score/{id}", methods={"PATCH"})
     * @return Response
     *
     * funkce pro úpravu skóre hry
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

        $this->check_win($game, $data);
        dump($game);
        dump($data);


        $response = new Response();
        $response->send();
        return $response;
    }

    /**
     * @param $game
     * @param $data
     *
     * funkce pro kontrolu dokončení hry
     */
    public function check_win ($game, $data) {
        if (!in_array(" ", $data->team1) && !in_array(" ", $data->team2)) {
            $team1_score = 0;
            $team2_score = 0;
            foreach (array_combine($data->team1, $data->team2) as $points1 => $points2) {
                if ($points1 > $points2) {
                    $team1_score++;
                } else if ($points1 < $points2) {
                    $team2_score++;
                } // todo else remiza
            }

            $winnerID = null;

            if ($team1_score > $team2_score) {
                $winnerID = $data->team1ID;
            } else if ($team1_score < $team2_score) {
                $winnerID = $data->team2ID;
            } // todo else remize

            if ($game->getNextGame() != null) {
                if ($game->getFirstInNextGame()) {
                    $game->getNextGame()->setTeam1ById($winnerID);
                } else {
                    $game->getNextGame()->setTeam2ById($winnerID);
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }
    }
}
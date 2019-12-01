<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Exp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// controller pro správe jednotlivých her turnaje
class GameController extends AbstractController {
    const SPIDER = 1;
    const ORDERING = 2;

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

        if ($game->getType() == self::ORDERING) {
            if ($this->check_order_ready($game->getTournament())) {
                $this->set_order($game->getTournament());
                $this->prepare_to_generate_map($game->getTournament()->getGames());
            }
        }

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

    public function check_order_ready($tournament) {
        foreach ($tournament->getGames() as $game) {
            if ($game->getType() == self::ORDERING) {
                if ($game->getWinner() == null) {
                    return false;
                }
            }
        }
        return true;
    }

    public function get_tournament_team($tournament, $team){
        $entityManager = $this->getDoctrine()->getManager();
        $tournament_team_table = $entityManager->getRepository(Exp::class)->findAll();
        foreach ($tournament_team_table as $tournament_team) {
            if ($tournament_team->getTournament() === $tournament and $tournament_team->getTeam() === $team) {
                return $tournament_team;
            }
        }
        return null;
    }

    public function set_exp ($tournament, $team, $exp) {
        $tournament_team = $this->get_tournament_team($tournament, $team);
        if ($tournament_team == null) {
            $tournament_team = new Exp();
            $tournament_team->setTournament($tournament);
            $tournament_team->setTeam($team);
        }
        $tournament_team->setExp($exp);
        $this->getDoctrine()->getManager()->persist($tournament_team);
        $this->getDoctrine()->getManager()->flush();
    }

    public function get_exp ($tournament, $team) {
        $tournament_team = $this->get_tournament_team($tournament, $team);
        if ($tournament_team != null) {
            return $tournament_team->getExp();
        }
        return null;
    }


    public function set_order($tournament) {
        //reset order
        $teams = $tournament->getTeams();
        foreach ($teams as $team) {
            $this->set_exp($tournament, $team, 0);

            //TODO smazat
//            $team->setExp(0, $tournament->getId());
//            $this->getDoctrine()->getManager()->persist($team);
//            $this->getDoctrine()->getManager()->flush();
        }

        $games = $tournament->getGames();

        foreach($games as $game) {
            if ($game->getType() == self::ORDERING) {
                $winner = $game->getWinner();
                $this->set_exp ($tournament, $winner, $this->get_exp($tournament, $winner));

                //TODO smazat
//                $winner->setExp($winner->getExp()+1);
//                $this->getDoctrine()->getManager()->persist($winner);
//                $this->getDoctrine()->getManager()->flush();
            }
        }
    }

    private $tournament;

    public function prepare_to_generate_map($games) {
        $group1 = [];
        $group2 = [];
        foreach ($games as $game) {
            if ($game->getType() == self::ORDERING) {
                if ($game->getRound() == 1) {
                    if (!in_array($game->getTeam1(), $group1))
                        array_push($group1, $game->getTeam1());
                    if (!in_array($game->getTeam2(), $group1))
                        array_push($group1, $game->getTeam2());
                }
                if ($game->getRound() == 2) {
                    if (!in_array($game->getTeam1(), $group2))
                        array_push($group2, $game->getTeam1());
                    if (!in_array($game->getTeam2(), $group2))
                        array_push($group2, $game->getTeam2());
                }
            }
        }
        $this->tournament = $games[0]->getTournament();
        usort($group1, function($a, $b) {
            $tournament = $this->tournament;
            return $this->get_exp($tournament, $a) - $this->get_exp($tournament, $b);
        });
        usort($group2, function($b, $a) {
            $tournament = $this->tournament;
            return $this->get_exp($tournament, $a) - $this->get_exp($tournament, $b);
        });

        // TODO smazat
        //usort($group1, function($a, $b) {return $a->getExp() - $b->getExp();});
        //usort($group2, function($b, $a) {return $a->getExp() - $b->getExp();});

        $sorted_teams = [];
        for ($i = 0; $i < count($group1); $i++) {
            array_push($sorted_teams, $group1[$i]);
            array_push($sorted_teams, $group2[$i]);
        }

        $i = 1;
        foreach ($sorted_teams as $team) {
            $this->set_exp($this->tournament, $team, $i);
            //TODO smazat
            //$team->setExp($i);
//            $this->getDoctrine()->getManager()->persist($team);
//            $this->getDoctrine()->getManager()->flush();
            $i++;
        }
    }
}
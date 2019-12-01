<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\Exp;
use App\Repository\TournamentRepository;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




/**
 * Class MapController
 * @package App\Controller
 *
 * kontroler pro správu pavouka
 */
class MapController extends TournamentController {
    const SPIDER = 1;
    const ORDERING = 2;

    public function sort_teams_by_groups($teams, $slice_count) {
//        $teams_count = count($teams);
//        $group1 = array_slice($teams, 0, $teams_count / 2);
//        $group2 = array_slice($teams, $teams_count / 2);
        $group1 = [];
        $group2 = [];
        for ($i = 0; $i < count($teams); $i++) {
            if ($i % 2 == 0) {
                array_push($group1, $teams[$i]);
            } else {
                array_push($group2, $teams[$i]);
            }
        }
        // mám group1 a group2

        $sorted_teams = [];
        $j = count($group2);
        for ($i = 0; $i < count($group1); $i++) {
            $j--;
            dump(array($i, $j));
            if ($i % 2 == 0) {
                array_unshift($sorted_teams, $group1[$i]);
                array_unshift($sorted_teams, $group2[$j]);
            } else {
                array_push($sorted_teams, $group1[$i]);
                array_push($sorted_teams, $group2[$j]);
            }
        }
        return $sorted_teams;
    }

    /**
     * @param $tournament
     * @return array
     *
     * funkce pro vytvoření prázdného prvotního kola
     */
    public function make_first ($tournament, $type) {
        $entityManager = $this->getDoctrine()->getManager();

        $teams = [];
        $teams_count = count($teams);
        // vztvoří nulté hry pro nejslabší, tak aby v prvním kole bylo 2^n týmů
        $square = [0, 2, 4, 8, 16, 32, 64];
        if ($type == self::ORDERING) {
            $teams = iterator_to_array($tournament->getTeams());
            $this->tournament = $tournament;
            // sorting podle výsledků
            usort($teams, function($a, $b) {
                $tournament = $this->tournament;
                return $this->get_exp($tournament, $a) - $this->get_exp($tournament, $b);
            });

            //// TODO ořez na 8 týmů
            $teams = array_slice($teams, 0, 8);
            dump($teams);
            ////// mám týmy seřazený podle exp


            // sorting aby to udělalo správně pavouka
            $slice_count = 0;
            $teams_count = count($teams);
            while (!in_array($teams_count, $square)) {
                $slice_count++;
                $teams_count--;
            }
            $teams = $this->sort_teams_by_groups($teams, $slice_count);

        }
        if ($type == self::SPIDER) {
            $teams = $tournament->getShuffledTeams();
        }


        $i = 0;
        $game = null;
        $games_round1 = [];
        $lvl = 0;

        $teams_count = count($teams);
        while (!in_array($teams_count, $square)) {
            $lvl = 1;
            $team = array_pop($teams);
            if ($i % 2 ==  0) {
                $game = new Game();
                $game->setTeam1($team);
            } else {
                $game->setTeam2($team);
                $game->setTournament($tournament);
                $game->setRound($lvl);
                // naplnen0 bod; teamu prázdným polem o počtu prvcích, kolik her má turnaj mít.
                $array = [];
                for ($j = 1; $j <= $tournament->getPlaysInGame(); $j++) {
                    array_push($array, " ");
                }
                $game->setPointsTeam1($array);
                $game->setPointsTeam2($array);

                $game->setType(self::SPIDER);
                $entityManager->persist($game);
                $entityManager->flush();

                //vytvoření hry do dalšího kola
                $nextgame = new Game();
                $team = array_pop($teams);
                $nextgame->setTeam1($team);
                $nextgame->setTournament($tournament);
                $nextgame->setRound($lvl+1);
                $game->setNextGame($nextgame);
                $game->setFirstInNextGame(false);
                $nextgame->setPointsTeam1($array);
                $nextgame->setPointsTeam2($array);
                $nextgame->setType(self::SPIDER);
                $entityManager->persist($nextgame);
                $entityManager->flush();
                //array_splice( $games_round1, count($games_round1)/2, 0, array($nextgame));
                array_push($games_round1, $nextgame);

                $teams_count--;
            }
            $i++;
        }
        //exit;

        $lvl++;
        // pole přidaných her
        $game = null;
        $games_round2 = [];
        $i = 0;
        foreach ($teams as $team) {
            if ($i % 2 ==  0) {
                $game = new Game();
                $game->setTeam1($team);
                $game->setType(self::SPIDER);
            } else {
                $game->setTeam2($team);
                $game->setTournament($tournament);
                $game->setRound($lvl);
                // naplnen0 bod; teamu prázdným polem o počtu prvcích, kolik her má turnaj mít.
                $array = [];
                for ($j = 1; $j <= $tournament->getPlaysInGame(); $j++) {
                    array_push($array, " ");
                }
                $game->setPointsTeam1($array);
                $game->setPointsTeam2($array);

                $game->setType(self::SPIDER);
                $entityManager->persist($game);
                $entityManager->flush();

                // nevím proč, ale nemůžu si to vythánout pomocí $tournament->getGames() :(
                array_push($games_round2, $game);
            }
            $i++;
        }
        
        if ($games_round1 != []) {
            $games_round1_count = count($games_round1);
            $games_round1_group1 = array_slice($games_round1, 0, $games_round1_count / 2);
            $games_round1_group2 = array_slice($games_round1, $games_round1_count / 2);

            $games_round2_count = count($games_round2);
            $games_round2_group1 = array_slice($games_round2, 0, $games_round2_count / 2);
            $games_round2_group2 = array_slice($games_round2, $games_round2_count / 2);
            //dump(array($games_round2_group1, $games_round1_group1, $games_round1_group2, $games_round2_group2));
            $games = array_merge($games_round2_group1, $games_round1_group1, $games_round1_group2, $games_round2_group2);
            //$games = array_merge($games_round1, $games_round2);
        } else {
            $games = $games_round2;
        }
        dump(array("games", $games));
        $this->add_order_to_games($games);
        return array($games, $lvl);
    }

    public function add_order_to_games($games) {
        $entityManager = $this->getDoctrine()->getManager();
        $i = 0;
        foreach ($games as $game) {
            $i++;
            $game->setDisplayOrder($i);
            $entityManager->persist($game);
            $entityManager->flush();
        }
    }

    /**
     * @param $tournament
     * @param $games
     * @param int $lvl
     *
     * funkce pro vytvoření zbytku pavouka
     */
    public function make_rest($tournament, $games, $lvl = 2){
        $entityManager = $this->getDoctrine()->getManager();
        $next_games = [];
        $i = 0;
        $newGame = null;
        foreach ($games as $game) {
            if ($game->getRound() == $lvl-1) {
                if ($i % 2 == 0) {
                    $newGame = new Game();
                    $newGame->setTournament($tournament);
                    $newGame->setRound($lvl);
                    $game->setNextGame($newGame);
                    $game->setFirstInNextGame(true);
                    $array = [];
                    for ($j = 1; $j <= $tournament->getPlaysInGame(); $j++) {
                        array_push($array, " ");
                    }
                    $newGame->setPointsTeam1($array);
                    $newGame->setPointsTeam2($array);

                    $newGame->setType(self::SPIDER);
                    $entityManager->persist($newGame);
                    $entityManager->flush();
                    array_push($next_games, $newGame);
                } else {
                    $game->setNextGame($newGame);
                    $game->setFirstInNextGame(false);
                    $game->setType(self::SPIDER);
                    $entityManager->persist($game);
                    $entityManager->flush();
                }
            }
            $i++;
        }
        if ($i > 2) {
            $this->make_rest($tournament, $next_games, $lvl+1);
        }
    }

    public function clear_games_in_tournament($tournament) {
        $games = array_filter(iterator_to_array($tournament->getGames()), array($this, "filter_spider"));
        $entityManager = $this->getDoctrine()->getManager();
        // odstranění případných her co by dělaly binec
        if ($games != []) {
            foreach ($games as $game) {
                $entityManager->remove($game);
                $entityManager->flush();
            }
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/tournaments/detail/{id}/generate/{type}", name="generate", methods={"GET", "POST"})
     *
     * funkce pro generování celého pavouka
     */
    public function generate(Request $request, $id, $type) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $this->clear_games_in_tournament($tournament);

        $lvl = null;
        list($games,$lvl) = $this->make_first($tournament, $type);
        $this->make_rest($tournament, $games, $lvl+1);

        $response = new Response();
        $response->send();
        return $response;
    }


    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/tournaments/detail/{id}/map", name="map", methods={"GET", "POST"})
     *
     * funkce pro zobrazení pavouka
     */
    public function map(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $games_obj = array_filter(iterator_to_array($tournament->getGames()), array($this, "filter_spider"));
        $games = [];
        foreach ($games_obj as $game) {
            array_push($games, ["id" => $game->getId(), "team1" => $game->getTeam1(), "team2" => $game->getTeam2(),
                "PointsTeam1" => $game->getPointsTeam1(), "PointsTeam2" => $game->getPointsTeam2(),
                "round" => $game->getRound(), "DisplayOrder" => $game->getDisplayOrder()]);
        }
        dump($games);
        // se5ayen9 podle kola
        //usort($games, function($a, $b) {return $a->getRound() - $b->getRound();});
        $sort = array();
        foreach($games as $k=>$v) {
            $sort['round'][$k] = $v['round'];
            $sort['DisplayOrder'][$k] = $v['DisplayOrder'];
        }
        array_multisort($sort['round'], SORT_ASC, $sort['DisplayOrder'], SORT_ASC,$games);

        dump($games);
        $admin = $tournament->getAdminString();
        $group1 = [];
        $group2 = [];
        return $this->render('pages/map/map.html.twig', array('tournament' => $tournament,'games' => $games,
            'type' => "map", 'admin' => $admin, 'group1' => $group1, 'group2' => $group2));
    }

    ///ORDERING

    public static $map_teams = [];

    static function filter_ordering($var) {
        return $var->getType() == self::ORDERING;
    }

    static function filter_spider($var) {
        return $var->getType() == self::SPIDER;
    }

    public function get_exp ($tournament, $team) {
        $entityManager = $this->getDoctrine()->getManager();
        $tournament_team_table = $entityManager->getRepository(Exp::class)->findAll();
        foreach ($tournament_team_table as $tournament_team) {
            if ($tournament_team->getTournament() === $tournament and $tournament_team->getTeam() === $team) {
                return $tournament_team->getExp();
            }
        }
        return null;
    }

    private $tournament;

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/tournaments/detail/{id}/ordering", name="ordering", methods={"GET", "POST"})
     *
     * funkce pro zobrazení pavouka
     */
    public function ordering(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $games = array_filter(iterator_to_array($tournament->getGames()),  array($this, "filter_ordering"));
        $admin = $tournament->getAdminString();
        $group1 = [];
        $group2 = [];
        foreach ($games as $game) {
            $team1 = ["id" => $game->getTeam1()->getId(), "name" => $game->getTeam1()->getName(), "exps" => $this->get_exp($tournament, $game->getTeam1())];
            $team2 = ["id" => $game->getTeam2()->getId(), "name" => $game->getTeam2()->getName(), "exps" => $this->get_exp($tournament, $game->getTeam2())];

            //$team2 = array_merge(json_decode(json_encode($game->getTeam2()), true), ["exps" => $this->get_exp($tournament, $game->getTeam2())]);
//            dump($game->getTeam1()->getId());
//            dump($game->getTeam1()->getName());
//            dump($team2);
//            exit;
            if ($game->getRound() == 1) {
                if (!in_array($team1, $group1))
                    array_push($group1, $team1);
                if (!in_array($team2, $group1))
                    array_push($group1,$team2);
            }
            if ($game->getRound() == 2) {
                if (!in_array($team1, $group2))
                    array_push($group2, $team1);
                if (!in_array($team2, $group2))
                    array_push($group2, $team2);
            }
        }

        $this->tournament = $tournament;
        usort($group1, function($b, $a) {
            $tournament = $this->tournament;
            return $this->get_exp($tournament, $a) - $this->get_exp($tournament, $b);
        });
        usort($group2, function($b, $a) {
            $tournament = $this->tournament;
            return $this->get_exp($tournament, $a) - $this->get_exp($tournament, $b);
        });

        // TODO delete
//        usort($group1, function($b, $a) {return $a->getExp() - $b->getExp();});
//        usort($group2, function($b, $a) {return $a->getExp() - $b->getExp();});
        return $this->render('pages/map/map.html.twig', array('tournament' => $tournament,'games' => $games,
            'type' => "ordering", 'admin' => $admin, 'group1' => $group1, 'group2' => $group2));
    }


    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/tournaments/detail/{id}/generate_ordering", name="generate_ordering", methods={"GET", "POST"})
     *
     * funkce pro generování turnaje všichni proti všem
     */
    public function generate_ordering(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $games = array_filter(iterator_to_array($tournament->getGames()), array($this, "filter_ordering"));
        $entityManager = $this->getDoctrine()->getManager();
        // odstranění případných her co by dělaly binec
        if ($games != []) {
            foreach ($games as $game) {
                $entityManager->remove($game);
                $entityManager->flush();
            }
        }

        $this->make_ordering($tournament);

        $response = new Response();
        $response->send();
        return $response;
    }

    /**
     * @param $tournament
     */
    public function make_ordering($tournament) {
        $teams = $tournament->getShuffledTeams();
        $teams_count = count($teams);
        $group1 = array_slice($teams, 0, $teams_count / 2);
        $group2 = array_slice($teams, $teams_count / 2);
        $this->generate_all_vs_all($group1, $tournament, 1);
        $this->generate_all_vs_all($group2, $tournament, 2);
    }

    /**
     * @param $teams
     * @param $tournament
     */
    public function generate_all_vs_all($teams, $tournament, $lvl) {
        $entityManager = $this->getDoctrine()->getManager();
        $teams_count = count($teams);

        for ($i = 0; $i < $teams_count; $i++) {
            for ($j = $i+1; $j < $teams_count; $j++) {
                $newGame = new Game();
                $newGame->setTournament($tournament);
                $newGame->setRound($lvl);
                $newGame->setTeam1($teams[$i]);
                $newGame->setTeam2($teams[$j]);
                $array = [];
                for ($k = 1; $k <= $tournament->getPlaysInGame(); $k++) {
                    array_push($array, " ");
                }
                $newGame->setPointsTeam1($array);
                $newGame->setPointsTeam2($array);

                $newGame->setType(self::ORDERING);
                $entityManager->persist($newGame);
                $entityManager->flush();
            }
        }
    }
}
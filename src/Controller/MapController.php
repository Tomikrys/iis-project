<?php


namespace App\Controller;


use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tournament;
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

class MapController extends TournamentController {

    public function make_first ($tournament) {
        $teams = $tournament->getShuffledTeams();
        $entityManager = $this->getDoctrine()->getManager();
        // pole přidaných her
        $game = null;
        $games = [];
        $i = 0;
        foreach ($teams as $team) {
            if ($i % 2 ==  0) {
                $game = new Game();
                $game->setTeam1($team);
            } else {
                $game->setTeam2($team);
                $game->setTournament($tournament);
                $game->setRound(1);
                // naplnen0 bod; teamu prázdným polem o počtu prvcích, kolik her má turnaj mít.
                $array = [];
                for ($j = 1; $j <= $tournament->getPlaysInGame(); $j++) {
                    array_push($array, " ");
                }
                $game->setPointsTeam1($array);
                $game->setPointsTeam2($array);

                $entityManager->persist($game);
                $entityManager->flush();

                // nevím proč, ale nemůžu si to vythánout pomocí $tournament->getGames() :(
                array_push($games, $game);
            }
            $i++;
        }
        return $games;
    }

    public function make_rest($tournament, $games, $lvl = 2){
        $entityManager = $this->getDoctrine()->getManager();
        $next_games = [];
        $i = 0;
        foreach ($games as $game) {
            $i++;
            if ($game->getRound() == $lvl-1) {
                if ($i % 2 == 0) {
                    $game = new Game();
                    $game->setTournament($tournament);
                    $game->setRound($lvl);
                    $array = [];
                    for ($j = 1; $j <= $tournament->getPlaysInGame(); $j++) {
                        array_push($array, " ");
                    }
                    $game->setPointsTeam1($array);
                    $game->setPointsTeam2($array);

                    $entityManager->persist($game);
                    $entityManager->flush();
                    array_push($next_games, $game);
                }
            }
        }
        if ($i >= 2) {
            $this->make_rest($tournament, $next_games, $lvl+1);
        }
    }

    /**
     * @Route("/tournaments/detail/{id}/generate", name="generate", methods={"GET", "POST"})
     */
    public function generate(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $games = $tournament->getGames();
        $entityManager = $this->getDoctrine()->getManager();
        // odstranění případných her co by dělaly binec
        if ($games != []) {
            foreach ($games as $game) {
                $entityManager->remove($game);
                $entityManager->flush();
            }
        }

        $games = $this->make_first($tournament);
        $this->make_rest($tournament, $games);

        $response = new Response();
        $response->send();
        return $response;
    }

    /**
     * @Route("/tournaments/detail/{id}/map", name="map", methods={"GET", "POST"})
     */
    public function map(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $games = iterator_to_array($tournament->getGames());
        usort($games, function($a, $b) {return $a->getRound() - $b->getRound();});

        return $this->render('pages/map/map.html.twig', array('tournament' => $tournament,'games' => $games));
    }
}
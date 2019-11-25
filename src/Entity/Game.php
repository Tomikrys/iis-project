<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Game
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 *
 * třída všech her
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     */
    private $team2;

    /**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    /**
     * @ORM\Column(type="integer")
     */
    private $round;

    /**
     * @ORM\Column(type="json")
     */
    private $points_team1 = [];

    /**
     * @ORM\Column(type="json")
     */
    private $points_team2 = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $FirstInNextGame;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\game")
     */
    private $NextGame;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?Team
    {
        return $this->team1;
    }

    public function setTeam1(?Team $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function setTeam1ById ($id) {
        $teams =  $this->getTournament()->getTeams();
        foreach ($teams as $team) {
            if ($team->getId() == $id) {
                break;
            }
        }
        $this->setTeam1($team);
    }

    public function getTeam2(): ?Team
    {
        return $this->team2;
    }

    public function setTeam2(?Team $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function setTeam2ById ($id) {
        $teams =  $this->getTournament()->getTeams();
        foreach ($teams as $team) {
            if ($team->getId() == $id) {
                break;
            }
        }
        $this->setTeam2($team);
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getPointsTeam1(): ?array
    {
        return $this->points_team1;
    }

    public function setPointsTeam1(?array $points_team1): self
    {
        $this->points_team1 = $points_team1;

        return $this;
    }

    public function getPointsTeam2(): ?array
    {
        return $this->points_team2;
    }

    public function setPointsTeam2(?array $points_team2): self
    {
        $this->points_team2 = $points_team2;

        return $this;
    }

    public function getFirstInNextGame(): ?bool
    {
        return $this->FirstInNextGame;
    }

    public function setFirstInNextGame(?bool $FirstInNextGame): self
    {
        $this->FirstInNextGame = $FirstInNextGame;

        return $this;
    }

    public function getNextGame(): ?game
    {
        return $this->NextGame;
    }

    public function setNextGame(?game $NextGame): self
    {
        $this->NextGame = $NextGame;

        return $this;
    }

}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team2;

    /**
     * @ORM\Column(type="integer")
     */
    private $field;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points_team1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points_team2;

    /**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

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

    public function getTeam2(): ?Team
    {
        return $this->team2;
    }

    public function setTeam2(?Team $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getField(): ?int
    {
        return $this->field;
    }

    public function setField(int $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getPointsTeam1(): ?int
    {
        return $this->points_team1;
    }

    public function setPointsTeam1(?int $points_team1): self
    {
        $this->points_team1 = $points_team1;

        return $this;
    }

    public function getPointsTeam2(): ?int
    {
        return $this->points_team2;
    }

    public function setPointsTeam2(?int $points_team2): self
    {
        $this->points_team2 = $points_team2;

        return $this;
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
}

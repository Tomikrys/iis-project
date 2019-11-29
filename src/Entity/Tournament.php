<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TournamentRepository")
 *
 * třída všech turnajů
 */
class Tournament
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(message="Cena musí být kladné číslo větší jak nula.")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(
     *     message="Počet hřišť musí být kladné číslo větší jak nula."
     * )
     */
    private $field_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(
     *     message="Maximální počet týmů musí být kladné číslo větší jak nula."
     * )
     */
    private $max_teams_count;

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="tournaments")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="tournament")
     */
    private $games;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *     message="Počet setů ve hře musí být kladné číslo větší jak nula."
     * )
     */
    private $plays_in_game;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tournaments")
     */
    private $admin;


    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->admin = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFieldCount(): ?int
    {
        return $this->field_count;
    }

    public function setFieldCount(?int $field_count): self
    {
        $this->field_count = $field_count;

        return $this;
    }

    public function getMaxTeamsCount(): ?int
    {
        return $this->max_teams_count;
    }

    public function setMaxTeamsCount(?int $max_teams_count): self
    {
        $this->max_teams_count = $max_teams_count;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @return array
     */
    public function getShuffledTeams(): array {
        $array_teams = iterator_to_array($this->teams);
        shuffle($array_teams);
        return $array_teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setTournament($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getTournament() === $this) {
                $game->setTournament(null);
            }
        }

        return $this;
    }

    public function getPlaysInGame(): ?int
    {
        return $this->plays_in_game;
    }

    public function setPlaysInGame(int $plays_in_game): self
    {
        $this->plays_in_game = $plays_in_game;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function getAdminString(): ?string {
        if ($this->admin) {
            return $this->admin->getEmail();
        } else {
            return "";
        }
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

}

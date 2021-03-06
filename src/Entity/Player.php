<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Player
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 *
 * třída všech hráčů
 */
class Player
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
     * @ORM\Column(type="boolean")
     */
    private $is_girl;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(
     *     pattern="/^(\+[0-9]{2,3})? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/",
     *     message="Telefonní číslo '{{ value }}' je ve špatném formátu."
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(
     *     message = "Email '{{ value }}' je ve špatném formátu."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path_to_logo;

    /**
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="players")
     */
    private $teams;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="players")
     */
    private $admin;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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

    public function getGender(): ?string
    {
        if ($this->is_girl) {
            return "žena";
        } else {
            return "muž";
        }
    }

    public function setGender(string $gender): ?string
    {
        if ($gender == "muž") {
            $this->is_girl = false;
        } else {
            $this->is_girl = true;
        }
    }

    public function getIsGirl(): ?bool
    {
        return $this->is_girl;
    }

    public function setIsGirl(bool $is_girl): self
    {
        $this->is_girl = $is_girl;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPathToLogo(): ?string
    {
        return $this->path_to_logo;
    }

    public function setPathToLogo(?string $path_to_logo): self
    {
        $this->path_to_logo = $path_to_logo;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addPlayer($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removePlayer($this);
        }

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

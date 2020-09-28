<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Portrait::class, mappedBy="owner", orphanRemoval=true)
     */
    private $portraits;

    public function __construct()
    {
        $this->portraits = new ArrayCollection();
    }

    public function __toString(){
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Portrait[]
     */
    public function getPortraits(): Collection
    {
        return $this->portraits;
    }

    public function addPortrait(Portrait $portrait): self
    {
        if (!$this->portraits->contains($portrait)) {
            $this->portraits[] = $portrait;
            $portrait->setOwner($this);
        }

        return $this;
    }

    public function removePortrait(Portrait $portrait): self
    {
        if ($this->portraits->contains($portrait)) {
            $this->portraits->removeElement($portrait);
            // set the owning side to null (unless already changed)
            if ($portrait->getOwner() === $this) {
                $portrait->setOwner(null);
            }
        }

        return $this;
    }
}

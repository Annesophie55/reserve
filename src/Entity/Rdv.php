<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RdvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: RdvRepository::class)]

class Rdv
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "boolean")]
    private ?bool $status = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\ManyToOne(inversedBy: 'rdv')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details = null;


    // Vos getters et setters restent inchangés

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getHeureDebut(): ?\DateTimeImmutable
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(?\DateTimeImmutable $heure_debut): self
    {
        $this->heure_debut = $heure_debut;
        return $this;
    }

    public function getHeureFin(): ?\DateTimeImmutable
    {
        return $this->heure_fin;
    }

    public function setHeureFin(?\DateTimeImmutable $heure_fin): self
    {
        $this->heure_fin = $heure_fin;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }

}

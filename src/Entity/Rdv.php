<?php

namespace App\Entity;

use DateInterval;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RdvRepository;

#[ORM\Entity(repositoryClass: RdvRepository::class)]
class Rdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?array $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dayHour = null;

    #[ORM\Column(type: Types::INTERVAL, nullable: true)]
    private ?DateInterval $duration = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }

    public function setStatus(?array $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDayHour(): ?\DateTimeInterface
    {
        return $this->dayHour;
    }

    public function setDayHour(?\DateTimeInterface $dayHour): static
    {
        $this->dayHour = $dayHour;

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}

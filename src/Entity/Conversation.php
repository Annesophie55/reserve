<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: User::class)]
    private Collection $client;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'adminConversation')]
    private Collection $adminConversation;

    public function __construct()
    {
        $this->client = new ArrayCollection();
        $this->adminConversation = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(User $client): static
    {
        if (!$this->client->contains($client)) {
            $this->client->add($client);
            $client->setConversation($this);
        }

        return $this;
    }

    public function removeClient(User $client): static
    {
        if ($this->client->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getConversation() === $this) {
                $client->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAdminConversation(): Collection
    {
        return $this->adminConversation;
    }

    public function addAdminConversation(User $adminConversation): static
    {
        if (!$this->adminConversation->contains($adminConversation)) {
            $this->adminConversation->add($adminConversation);
        }

        return $this;
    }

    public function removeAdminConversation(User $adminConversation): static
    {
        $this->adminConversation->removeElement($adminConversation);

        return $this;
    }
}

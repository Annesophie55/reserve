<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Rdv::class)]
    private Collection $rdv;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Note::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $sent;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $received;

    #[ORM\ManyToOne(inversedBy: 'client')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'adminConversation')]
    private Collection $adminConversation;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->rdv = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
        $this->adminConversation = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Rdv>
     */
    public function getRdv(): Collection
    {
        return $this->rdv;
    }

    public function addRdv(Rdv $rdv): static
    {
        if (!$this->rdv->contains($rdv)) {
            $this->rdv->add($rdv);
            $rdv->setUser($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): static
    {
        if ($this->rdv->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getUser() === $this) {
                $rdv->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Message $sent): static
    {
        if (!$this->sent->contains($sent)) {
            $this->sent->add($sent);
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Message $sent): static
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Message $received): static
    {
        if (!$this->received->contains($received)) {
            $this->received->add($received);
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Message $received): static
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getAdminConversation(): Collection
    {
        return $this->adminConversation;
    }

    public function addAdminConversation(Conversation $adminConversation): static
    {
        if (!$this->adminConversation->contains($adminConversation)) {
            $this->adminConversation->add($adminConversation);
            $adminConversation->addAdminConversation($this);
        }

        return $this;
    }

    public function removeAdminConversation(Conversation $adminConversation): static
    {
        if ($this->adminConversation->removeElement($adminConversation)) {
            $adminConversation->removeAdminConversation($this);
        }

        return $this;
    }
}

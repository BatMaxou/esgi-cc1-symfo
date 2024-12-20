<?php

namespace App\Entity;

use App\Entity\Comment\Comment;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var RoleEnum[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    /**
     * @var Collection<int, DisciplineSubscription>
     */
    #[ORM\OneToMany(targetEntity: DisciplineSubscription::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $disciplineSubscriptions;

    /**
     * @var Collection<int, Tutorial>
     */
    #[ORM\OneToMany(targetEntity: Tutorial::class, mappedBy: 'author')]
    private Collection $tutorials;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'user')]
    private Collection $ratings;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    public function __construct()
    {
        $this->disciplineSubscriptions = new ArrayCollection();
        $this->tutorials = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password, $alreadyHashed = false): static
    {
        if ($alreadyHashed) {
            $this->password = $password;
        } else {
            $this->plainPassword = $password;
            $this->password = null;
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return [RoleEnum::USER->value, ...array_map(fn (RoleEnum $role) => $role->value, $this->roles)];
    }

    public function addRole(RoleEnum $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(RoleEnum $role): static
    {
        $key = array_search($role, $this->roles, true);
        if (false !== $key) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection<int, DisciplineSubscription>
     */
    public function getDisciplineSubscriptions(): Collection
    {
        return $this->disciplineSubscriptions;
    }

    public function addDisciplineSubscription(DisciplineSubscription $disciplineSubscription): static
    {
        if (!$this->disciplineSubscriptions->contains($disciplineSubscription)) {
            $this->disciplineSubscriptions->add($disciplineSubscription);
            $disciplineSubscription->setUser($this);
        }

        return $this;
    }

    public function removeDisciplineSubscription(DisciplineSubscription $disciplineSubscription): static
    {
        if ($this->disciplineSubscriptions->removeElement($disciplineSubscription)) {
            // set the owning side to null (unless already changed)
            if ($disciplineSubscription->getUser() === $this) {
                $disciplineSubscription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tutorial>
     */
    public function getTutorials(): Collection
    {
        return $this->tutorials;
    }

    public function addTutorial(Tutorial $tutorial): static
    {
        if (!$this->tutorials->contains($tutorial)) {
            $this->tutorials->add($tutorial);
            $tutorial->setAuthor($this);
        }

        return $this;
    }

    public function removeTutorial(Tutorial $tutorial): static
    {
        if ($this->tutorials->removeElement($tutorial)) {
            // set the owning side to null (unless already changed)
            if ($tutorial->getAuthor() === $this) {
                $tutorial->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setUser($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getUser() === $this) {
                $rating->setUser(null);
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
}

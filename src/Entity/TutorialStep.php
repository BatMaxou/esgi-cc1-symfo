<?php

namespace App\Entity;

use App\Entity\Comment\TutorialStepComment;
use App\Repository\TutorialStepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialStepRepository::class)]
class TutorialStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $position = null;

    /**
     * @var Collection<int, TutorialStepComment>
     */
    #[ORM\OneToMany(targetEntity: TutorialStepComment::class, mappedBy: 'step')]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'tutorialSteps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tutorial $tutorial = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, TutorialStepComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(TutorialStepComment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setStep($this);
        }

        return $this;
    }

    public function removeComment(TutorialStepComment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getStep() === $this) {
                $comment->setStep(null);
            }
        }

        return $this;
    }

    public function getTutorial(): ?Tutorial
    {
        return $this->tutorial;
    }

    public function setTutorial(?Tutorial $tutorial): static
    {
        $this->tutorial = $tutorial;

        return $this;
    }
}

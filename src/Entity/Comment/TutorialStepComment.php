<?php

namespace App\Entity\Comment;

use App\Entity\TutorialStep;
use App\Repository\TutorialStepCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialStepCommentRepository::class)]
class TutorialStepComment extends Comment
{
    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorialStep $step = null;

    public function getStep(): ?TutorialStep
    {
        return $this->step;
    }

    public function setStep(?TutorialStep $step): static
    {
        $this->step = $step;

        return $this;
    }
}

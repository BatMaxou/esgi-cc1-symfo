<?php

namespace App\Entity\Comment;

use App\Entity\Tutorial;
use App\Repository\TutorialCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialCommentRepository::class)]
class TutorialComment extends Comment
{
    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tutorial $tutorial = null;

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

<?php

namespace App\Entity;

use App\Repository\DisciplineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisciplineRepository::class)]
class Discipline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, DisciplineSubscription>
     */
    #[ORM\OneToMany(targetEntity: DisciplineSubscription::class, mappedBy: 'discipline', orphanRemoval: true)]
    private Collection $disciplineSubscriptions;

    /**
     * @var Collection<int, Tutorial>
     */
    #[ORM\ManyToMany(targetEntity: Tutorial::class, mappedBy: 'disciplines')]
    private Collection $tutorials;

    public function __construct()
    {
        $this->disciplineSubscriptions = new ArrayCollection();
        $this->tutorials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $disciplineSubscription->setDiscipline($this);
        }

        return $this;
    }

    public function removeDisciplineSubscription(DisciplineSubscription $disciplineSubscription): static
    {
        if ($this->disciplineSubscriptions->removeElement($disciplineSubscription)) {
            // set the owning side to null (unless already changed)
            if ($disciplineSubscription->getDiscipline() === $this) {
                $disciplineSubscription->setDiscipline(null);
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
            $tutorial->addDiscipline($this);
        }

        return $this;
    }

    public function removeTutorial(Tutorial $tutorial): static
    {
        if ($this->tutorials->removeElement($tutorial)) {
            $tutorial->removeDiscipline($this);
        }

        return $this;
    }
}

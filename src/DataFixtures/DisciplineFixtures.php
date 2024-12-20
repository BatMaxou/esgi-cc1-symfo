<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Discipline;

class DisciplineFixtures extends Fixture
{
    use FakerFixtureTrait;

    private const DISCIPLINE = [
        'Mathématiques',
        'Physique',
        'Chimie',
        'Informatique',
        'Biologie',
        'Géologie',
        'Histoire',
        'Géographie',
        'Langues',
        'Littérature',
        'Philosophie',
        'Sociologie',
        'Psychologie',
        'Économie',
        'Droit',
        'Sciences politiques',
        'Médecine',
        'Art',
        'Musique',
        'Sport',
    ];

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createDisciplines();

        $manager->flush();
    }

    private function createDisciplines(): void
    {
        foreach (self::DISCIPLINE as $discipline) {
            $discipline = (new Discipline())
                ->setName($discipline);

            $this->manager->persist($discipline);
        }
    }
}

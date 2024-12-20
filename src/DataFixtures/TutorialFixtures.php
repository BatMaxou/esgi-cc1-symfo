<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Tutorial;
use App\Entity\TutorialStep;
use App\Entity\User;
use App\Repository\DisciplineRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TutorialFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly DisciplineRepository $disciplineRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createTutorials();

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            DisciplineFixtures::class,
        ];
    }

    private function createTutorials(): void
    {
        $admins = $this->userRepository->findAllAdmins();
        $disciplines = $this->disciplineRepository->findAll();

        foreach ($admins as $admin) {
            for ($i = 0; $i < $this->faker->numberBetween(20, 50); ++$i) {
                $this->createTutorial($admin, $this->faker->randomElements($disciplines, $this->faker->numberBetween(1, 2)));
            }
        }
    }

    private function createTutorial(User $admin, array $disciplines = []): void
    {
        $tutorial = (new Tutorial())
            ->setTitle($this->faker->sentence())
            ->setDescription($this->faker->paragraph())
            ->setAuthor($admin)
            ->setCreatedAt(new \DateTimeImmutable($this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')))
            ->setDifficulty($this->faker->numberBetween(0, 10))
            ->setDuration($this->faker->numberBetween(10, 120));

        $tutorial->setUpdatedAt(new \DateTimeImmutable($this->faker->dateTimeBetween($tutorial->getCreatedAt()->format('Y-m-d H:i:s'), 'now')->format('Y-m-d H:i:s')));

        foreach ($disciplines as $discipline) {
            $tutorial->addDiscipline($discipline);
        }

        $this->createTutorialSteps($tutorial);

        $this->manager->persist($tutorial);
    }

    private function createTutorialSteps(Tutorial $tutorial): void
    {
        for ($i = 1; $i <= $this->faker->numberBetween(1, 5); ++$i) {
            $this->createTutorialStep($tutorial, $i);
        }
    }

    private function createTutorialStep(Tutorial $tutorial, int $position = 1): void
    {
        $step = (new TutorialStep())
            ->setTitle($this->faker->sentence())
            ->setContent($this->faker->paragraphs(10, true))
            ->setPosition($position)
            ->setTutorial($tutorial);

        $this->manager->persist($step);
    }
}

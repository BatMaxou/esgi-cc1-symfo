<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Rating;
use App\Entity\Tutorial;
use App\Entity\User;
use App\Repository\TutorialRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RatingFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TutorialRepository $tutorialRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createRatings();

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TutorialFixtures::class,
        ];
    }

    private function createRatings(): void
    {
        $users = $this->userRepository->findAll();
        $tutorials = $this->tutorialRepository->findAll();

        foreach ($users as $user) {
            $alreadyRated = [];

            for ($i = 0; $i < $this->faker->numberBetween(0, 20); ++$i) {
                $tutorial = $this->faker->randomElement($tutorials);
                if (in_array($tutorial->getId(), $alreadyRated)) {
                    continue;
                }

                $alreadyRated[] = $tutorial->getId();
                $this->createRating($user, $tutorial);
            }
        }
    }

    private function createRating(User $user, Tutorial $tutorial): void
    {
        $rating = (new Rating())
            ->setUser($user)
            ->setTutorial($tutorial)
            ->setRate($this->faker->numberBetween(0, 100));

        $this->manager->persist($rating);
    }
}

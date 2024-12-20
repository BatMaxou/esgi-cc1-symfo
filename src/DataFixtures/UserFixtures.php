<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Entity\WatchHistory;
use App\Entity\PlaylistMedia;
use App\Enum\AccountStatusEnum;
use App\Entity\SubscriptionHistory;
use App\Repository\MediaRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\SubscriptionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Enum\CommentStatusEnum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        for ($i = 0; $i < 100; ++$i) {
            $user = $this->createUser();

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [];
    }

    private function createUser(): User
    {
        $user = (new User())
            ->setEmail($this->faker->email())
            ->setUsername($this->faker->userName());

        return $user->setPassword('azerty');
    }
}

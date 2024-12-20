<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\DisciplineSubscription;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\DisciplineRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    private array $disciplines;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly DisciplineRepository $disciplineRepository,
    ) {
        $this->initializeFaker();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->disciplines = $this->disciplineRepository->findAll();

        $this->createAdmins(3);
        $this->createUsers(20);
        $this->createBannedUsers(10);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DisciplineFixtures::class,
        ];
    }

    private function createDisciplineSubscriptions(User $user): void
    {
        $alreadyPickDisciplineIds = [];

        for ($i = 0; $i < $this->faker->numberBetween(0, 5); ++$i) {
            $discipline = $this->faker->randomElement($this->disciplines);
            $id = $discipline->getId();

            if (in_array($id, $alreadyPickDisciplineIds)) {
                continue;
            }

            $alreadyPickDisciplineIds[] = $id;
            $subscription = (new DisciplineSubscription())
                ->setUser($user)
                ->setDiscipline($discipline);

            $this->manager->persist($subscription);
        }
    }

    private function createUsers(int $count, ?callable $process = null): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $user = $process ? $process() : $this->createUser();
            $this->createDisciplineSubscriptions($user);

            $this->manager->persist($user);
        }
    }

    private function createAdmins(int $count): void
    {
        $this->createUsers($count, $this->createAdmin(...));
    }

    private function createBannedUsers(int $count): void
    {
        $this->createUsers($count, $this->createBannedUser(...));
    }

    private function createUser(): User
    {
        $user = (new User())
            ->setEmail($this->faker->email())
            ->setUsername($this->faker->userName());

        return $user->setPassword('azerty');
    }

    private function createAdmin(): User
    {
        $user = $this->createUser();
        $user->addRole(RoleEnum::ADMIN);

        return $user;
    }

    private function createBannedUser(): User
    {
        $user = $this->createUser();
        $user->addRole(RoleEnum::BANNED);

        return $user;
    }
}

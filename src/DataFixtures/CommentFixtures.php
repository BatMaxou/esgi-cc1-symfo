<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\Comment\TutorialComment;
use App\Entity\Comment\TutorialStepComment;
use App\Entity\User;
use App\Repository\TutorialRepository;
use App\Repository\TutorialStepRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerFixtureTrait {
        __construct as initializeFaker;
    }

    private ObjectManager $manager;
    private array $users;
    private array $tutorials;
    private array $tutorialSteps;
    private array $comments;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TutorialRepository $tutorialRepository,
        private readonly TutorialStepRepository $tutorialStepRepository,
    ) {
        $this->initializeFaker();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TutorialFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->users = $this->userRepository->findAll();
        $this->tutorials = $this->tutorialRepository->findAll();
        $this->tutorialSteps = $this->tutorialStepRepository->findAll();

        $this->createComments();

        $manager->flush();
    }

    private function createComments(): void
    {
        foreach ($this->users as $user) {
            $this->createParentComments($user);
            $this->createChildComments($user);
        }
    }

    private function createParentComments(User $user): void
    {
        for ($i = 0; $i < $this->faker->numberBetween(0, 5); ++$i) {
            $comment = $this->faker->boolean()
                ? $this->initTutorialComment()
                : $this->initTutorialStepComment();

            $comment
                ->setUser($user)
                ->setContent($this->faker->realText(200));

            $this->manager->persist($comment);

            $this->comments[] = $comment;
        }
    }

    private function createChildComments(User $user): void
    {
        foreach ($this->comments as $comment) {
            if ($this->faker->boolean(5)) {
                $childComment = match ($comment::class) {
                    TutorialComment::class => $this->initTutorialComment(),
                    TutorialStepComment::class => $this->initTutorialStepComment(),
                };

                $childComment
                    ->setUser($user)
                    ->setContent($this->faker->realText(200))
                    ->setParent($comment);

                $this->manager->persist($childComment);
            }
        }
    }

    private function initTutorialComment(): TutorialComment
    {
        return (new TutorialComment())->setTutorial($this->faker->randomElement($this->tutorials));
    }

    private function initTutorialStepComment(): TutorialStepComment
    {
        return (new TutorialStepComment())->setStep($this->faker->randomElement($this->tutorialSteps));
    }
}

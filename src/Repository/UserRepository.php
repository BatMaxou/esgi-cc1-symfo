<?php

namespace App\Repository;

use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllAdmins(): array
    {
        return $this->findAllByRole(RoleEnum::ADMIN);
    }

    private function findAllByRole(RoleEnum $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role->value.'"%')
            ->getQuery()
            ->getResult();
    }
}

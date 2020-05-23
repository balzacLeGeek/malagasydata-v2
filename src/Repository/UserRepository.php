<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByCredential(string $creadential): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :creadential OR u.email = :creadential')
            ->setParameter('creadential', $creadential)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getUsers(array $searchCriteria = [], bool $count = false, $returnQuery = false)
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $roles = [];

        if ($searchCriteria) {
            // Override $roles to the roles given in searchCriteria
            if (array_key_exists('roles', $searchCriteria)) {
                $roles = $searchCriteria['roles'];
            }

            // TODO
            // Add criterias conditions
        }

        // Search by Roles
        foreach ($roles as $key => $role) {
            $roleQuery = "'%" . $role . "%'";

            if ($key === 0) {
                $queryBuilder->andWhere('u.roles LIKE ' . $roleQuery);
            } else {
                $queryBuilder->orWhere('u.roles LIKE ' . $roleQuery);
            }
        }

        $queryBuilder->andWhere('u.username != \'admin\'');

        $query = $queryBuilder->orderBy('u.createdAt', 'DESC');

        if ($returnQuery) {
            return $query;
        }

        $result = $query
            ->getQuery()
            ->getResult();

        return $count ? [count($result)] : $result;
    }

    public function emailAlreadyExists(User $user): bool
    {
        $user = $this->createQueryBuilder('u')
            ->andWhere('u.email = :email AND u.id != :id')
            ->setParameters([
                'email' => $user->getEmail(),
                'id' => $user->getId(),
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $user !== null;
    }
}

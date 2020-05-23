<?php
/**
 * User Manager
 */
namespace App\Manager;

use App\Entity\User;
use App\Mybase\Manager\MybaseManager;
use App\Mybase\Services\Base\SBase;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager extends SBase {
    use MybaseManager;

    /**
     * @var UserRepository The entity repository
     */
    private $repository;

    /**
    * @var UserPasswordEncoderInterface $passwordEncoder
    */
    private $passwordEncoder;

    /**
     * User Manager constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($entityManager, $container);

        $this->repository = $this->getRepository(User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Register new User
     * 
     * @param User $user
     * @param string $plainPassword
     * @return User
     */
    public function registerUser(User $user, string $plainPassword): User
    {
        $encodedPassword = $this->encryptePassword($user, $plainPassword);

        $user->setPassword($encodedPassword);

        if (!$user->getUsername()) {
            $user->setUsername($user->getEmail());
        }

        return $this->save($user);
    }

    public function saveUpdate(User $user, string $plainPassword = null): User
    {
        if ($plainPassword) {
            $encodedPassword = $this->encryptePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        return $this->save($user);
    }

    public function toggleUserStatus(User $user): User
    {
        $user->setStatus(!$user->getStatus());

        return $this->save($user);
    }

    public function encryptePassword(User $user, string $plainPassword): string
    {
        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }
}

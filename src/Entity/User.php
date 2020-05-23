<?php

namespace App\Entity;

use App\Mybase\Security\User\UserUtils;
use App\Mybase\Security\User\AbstractUser;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends AbstractUser implements UserInterface
{
    const ADMIN         = 'ROLE_ADMIN';
    const USER          = 'ROLE_USER';

    const USER_DISABLED = 0;
    const USER_ENABLE   = 1;

    const SEXE_MALE = 'homme';
    const SEXE_FEMALE = 'femme';

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"default"})
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"default"})
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"default"})
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->status = self::USER_DISABLED;
        $this->createdAt = new \Datetime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_MODERATOR

        // TODO
        // Review default User role
        // $roles[] = self::USER;

        return array_unique($roles);
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(String $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * Get user principal role
     * 
     * @return string
     */
    public function getMainRole(): string
    {
        return $this->getRoles()[0];
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }
    
    public function setPassword(string $password, bool $validate = false): self
    {
        $this->password = $validate ? UserUtils::validatePassword($password) : $password;

        return $this;
    }

    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return (string) $this->plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /** @inheritDoc */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /** @inheritDoc */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /** @inheritDoc */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /** @inheritDoc */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /** @inheritDoc */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /** @inheritDoc */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @inheritDoc */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /** @inheritDoc */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = !$updatedAt ? new \Datetime() : $updatedAt;

        return $this;
    }
}

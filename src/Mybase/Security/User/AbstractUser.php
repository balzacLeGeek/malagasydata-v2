<?php

namespace App\Mybase\Security\User;

use App\Mybase\Security\User\UserUtils;

abstract class AbstractUser
{
    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $fullname;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $confirmationToken;

    /**
     * Get User lastname
     *
     * @return string|null
     */ 
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set User lastname
     *
     * @param string $lastname
     *
     * @return self
     */ 
    public function setLastname(?string $lastname, bool $format = true)
    {
        $lastname = $format ? strtoupper($lastname) : $lastname;

        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get User firstname
     *
     * @return string|null
     */ 
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set User firstname
     *
     * @param string $firstname
     *
     * @return self
     */ 
    public function setFirstname(?string $firstname, bool $format = true)
    {
        $firstname = $format ? ucfirst(strtolower($firstname)) : $firstname;

        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get Username
     *
     * @return string
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set Username
     *
     * @param string $username
     * @param bool $validate If true, the given value will be validated to respect rules described in UserUtils::validateUsername()
     *
     * @return self
     */ 
    public function setUsername(string $username, bool $validate = true)
    {
        $username = $validate ? UserUtils::validateUsername($username) : $username;

        $this->username = $username;

        return $this;
    }

    /**
     * Get User $confirmationToken
     *
     * @return string|null
     */ 
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * Set User $confirmationToken
     *
     * @param string $confirmationToken
     *
     * @return self
     */ 
    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Gets user firstname + lastname's
     * 
     * @return string|null
     */
    public function getFullname(): ?string
    {
        $lastname = $this->getLastname();
        $firstname = $this->getFirstname();

        if (!$lastname && !$firstname) {
            return null;
        }

        return trim($this->firstname . ' ' . $this->lastname);
    }

    public function __toString(): string
    {
        return $this->getFullname() ?: $this->username;
    }
}
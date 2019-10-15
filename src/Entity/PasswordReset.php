<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasswordResetRepository")
 * @ORM\Table(name="password_reset")
 */
class PasswordReset
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $cachedEmail;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordChangedAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=70, unique=true)
     */
    private $token;


    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setCreatedAt(new \DateTime());
        $this->setCachedEmail($user->getEmail());
        $this->setToken(sha1($user->getPassword() . uniqid()));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return PasswordReset
     */
    public function setUser(User $user): PasswordReset
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getCachedEmail(): string
    {
        return (string) $this->cachedEmail;
    }

    /**
     * @param string $cachedEmail
     * @return PasswordReset
     */
    public function setCachedEmail(string $cachedEmail): PasswordReset
    {
        $this->cachedEmail = $cachedEmail;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return PasswordReset
     * @throws \Exception
     */
    public function setCreatedAt(\DateTime $createdAt): PasswordReset
    {
        $this->createdAt = $createdAt;
        $expiresAt = $createdAt->add(new \DateInterval('PT1H'));
        $this->setExpiresAt($expiresAt);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     * @return PasswordReset
     */
    public function setExpiresAt(\DateTime $expiresAt): PasswordReset
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return (string) $this->token;
    }

    /**
     * @param string $token
     * @return PasswordReset
     */
    public function setToken(string $token): PasswordReset
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPasswordChangedAt(): ?\DateTime
    {
        return $this->passwordChangedAt;
    }

    /**
     * @param \DateTime $passwordChangedAt
     * @return PasswordReset
     */
    public function setPasswordChangedAt(\DateTime $passwordChangedAt): PasswordReset
    {
        $this->passwordChangedAt = $passwordChangedAt;
        return $this;
    }


}
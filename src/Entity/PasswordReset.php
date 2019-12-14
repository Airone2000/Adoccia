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
        $this->setToken(sha1($user->getPassword().uniqid()));
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCachedEmail(): string
    {
        return (string) $this->cachedEmail;
    }

    public function setCachedEmail(string $cachedEmail): self
    {
        $this->cachedEmail = $cachedEmail;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @throws \Exception
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        $expiresAt = $createdAt->add(new \DateInterval('PT1H'));
        $this->setExpiresAt($expiresAt);

        return $this;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): string
    {
        return (string) $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getPasswordChangedAt(): ?\DateTime
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(\DateTime $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;

        return $this;
    }
}

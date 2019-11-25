<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="picture")
 */
class Picture
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $filename;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $width;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $height;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $isTemp = true;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $uniqueId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return Picture
     */
    public function setTitle(?string $title): Picture
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Picture
     */
    public function setDescription(?string $description): Picture
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     * @return Picture
     */
    public function setSource(?string $source): Picture
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Picture
     */
    public function setUser(User $user): Picture
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Picture
     */
    public function setFilename(string $filename): Picture
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return Picture
     */
    public function setWidth(int $width): Picture
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Picture
     */
    public function setHeight(int $height): Picture
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemp(): bool
    {
        return $this->isTemp;
    }

    /**
     * @param bool $isTemp
     * @return Picture
     */
    public function setIsTemp(bool $isTemp): Picture
    {
        $this->isTemp = $isTemp;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     * @return Picture
     */
    public function setUniqueId(string $uniqueId): Picture
    {
        $this->uniqueId = $uniqueId;
        return $this;
    }

}
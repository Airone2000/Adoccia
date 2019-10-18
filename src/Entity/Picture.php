<?php

namespace App\Entity;

use App\Enum\PictureTargetEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 * @ORM\EntityListeners("App\EntityListener\PictureListener")
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @var UploadedFile|null
     * @Assert\Image(maxSize="5M")
     */
    private $uploadedFile;

    /**
     * @var Category|null
     * @ORM\OneToOne(targetEntity="App\Entity\Category", inversedBy="picture")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $category;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $public = PictureTargetEnum::ALL_PUBLIC;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $user;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * @param UploadedFile|null $uploadedFile
     * @return Picture
     */
    public function setUploadedFile(?UploadedFile $uploadedFile): Picture
    {
        $this->uploadedFile = $uploadedFile;
        $this->setUpdatedAt(new \DateTime());
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Picture
     */
    public function setCategory(?Category $category): Picture
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublic(): int
    {
        return $this->public;
    }

    /**
     * @param int $public
     * @return Picture
     */
    public function setPublic(int $public): Picture
    {
        $this->public = $public;
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
     * @param User|null $user
     * @return Picture
     */
    public function setUser(?User $user): Picture
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     * @return Picture
     */
    public function setUpdatedAt(?\DateTime $updatedAt): Picture
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}

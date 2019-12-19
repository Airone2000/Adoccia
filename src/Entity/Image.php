<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var Uuid|null|string
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private $filename;

    /**
     * @var ImageCrop|null
     * @ORM\OneToOne(targetEntity="App\Entity\ImageCrop", fetch="EAGER", inversedBy="image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $crop;

    /**
     * @return Uuid|string|null
     */
    public function getId()
    {
        return $this->id;
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
     * @return Image
     */
    public function setFilename(string $filename): Image
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return ImageCrop|null
     */
    public function getCrop(): ?ImageCrop
    {
        return $this->crop;
    }

    /**
     * @param ImageCrop|null $crop
     * @return Image
     */
    public function setCrop(?ImageCrop $crop): Image
    {
        $this->crop = $crop;
        return $this;
    }


}

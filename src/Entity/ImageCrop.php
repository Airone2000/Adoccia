<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageCropRepository")
 * @ORM\Entity
 * @ORM\Table(name="image_crop")
 */
class ImageCrop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="App\Entity\Image", mappedBy="crop")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $image;

    /**
     * @var int
     * @ORM\Column(type="string")
     */
    private $width;

    /**
     * @var int
     * @ORM\Column(type="string")
     */
    private $height;

    /**
     * @var int
     * @ORM\Column(type="string")
     */
    private $x;

    /**
     * @var int
     * @ORM\Column(type="string")
     */
    private $y;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     * @return ImageCrop
     */
    public function setImage(Image $image): ImageCrop
    {
        $this->image = $image;
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
     * @return ImageCrop
     */
    public function setWidth(int $width): ImageCrop
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
     * @return ImageCrop
     */
    public function setHeight(int $height): ImageCrop
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     * @return ImageCrop
     */
    public function setX(int $x): ImageCrop
    {
        $this->x = $x;
        return $this;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     * @return ImageCrop
     */
    public function setY(int $y): ImageCrop
    {
        $this->y = $y;
        return $this;
    }
}

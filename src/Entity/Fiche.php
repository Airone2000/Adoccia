<?php

namespace App\Entity;

use App\Validator\FichePicture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheRepository")
 * @ORM\EntityListeners("\App\EntityListener\FicheListener")
 */
class Fiche
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="fiches")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $category;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $creator;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(
     *     max="255"
     * )
     */
    private $title;

    /**
     * @var Picture|null
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $picture;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Value", cascade={"persist", "remove"}, mappedBy="fiche")
     * @Assert\Valid()
     */
    private $values;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $published = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $valid = true;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    public function getId(): ?int
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
     * @return Fiche
     */
    public function setTitle(?string $title): Fiche
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /**
     * @param Collection $values
     * @return Fiche
     */
    public function setValues(Collection $values): Fiche
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param Value $value
     * @return Fiche
     */
    public function addValue(Value $value): Fiche
    {
        $value->setFiche($this);
        $this->values->add($value);
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Fiche
     */
    public function setCategory(Category $category): Fiche
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $published
     * @return Fiche
     */
    public function setPublished(bool $published): Fiche
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     * @return Fiche
     */
    public function setValid(bool $valid): Fiche
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param User|null $creator
     * @return Fiche
     */
    public function setCreator(?User $creator): Fiche
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return Picture|null
     */
    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture|null $picture
     * @return Fiche
     */
    public function setPicture(?Picture $picture): Fiche
    {
        $this->picture = $picture;
        if ($picture instanceof Picture) {
            $picture->setIsTemp(false);
        }
        return $this;
    }

}

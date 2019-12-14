<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, fetch="EAGER")
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function setValues(Collection $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function addValue(Value $value): self
    {
        $value->setFiche($this);
        $this->values->add($value);

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;
        if ($picture instanceof Picture) {
            $picture->setIsTemp(false);
        }

        return $this;
    }
}

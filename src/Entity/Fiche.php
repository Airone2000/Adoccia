<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheRepository")
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
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(
     *     max="255"
     * )
     */
    private $title;

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

}

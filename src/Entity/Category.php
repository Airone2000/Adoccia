<?php

namespace App\Entity;

use App\Validator\CategoryPicture;
use App\Validator\Is169;
use App\Validator\PictureIsSquare;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\EntityListeners("\App\EntityListener\CategoryListener")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Category:Post", "Category:Put"})
     * @Assert\Length(max="255", groups={"Category:Post", "Category:Put"})
     */
    private $name;

    /**
     * @var Picture|null
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     *
     * @PictureIsSquare(groups={"Category:Post", "Category:Put"})
     */
    private $picture;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="categories")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var Form
     * @ORM\OneToOne(targetEntity="App\Entity\Form", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $form;

    /**
     * @var Form|null
     * @ORM\OneToOne(targetEntity="App\Entity\Form", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $draftForm;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Fiche", mappedBy="category", cascade={"remove"})
     */
    private $fiches;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $online = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $public = false;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setForm(new Form());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getDraftForm(): ?Form
    {
        return $this->draftForm;
    }

    public function setDraftForm(?Form $draftForm): self
    {
        $this->draftForm = $draftForm;

        return $this;
    }

    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function isOnline(): bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return Category
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
            $picture->setIsTemp(false); // No longer temps
        }

        return $this;
    }
}

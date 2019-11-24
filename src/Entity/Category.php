<?php

namespace App\Entity;

use App\Validator\CategoryPicture;
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
     * @var Picture|null
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, mappedBy="category")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     *
     * @CategoryPicture(groups={"Category:Post", "CategoryPut"})
     * @Assert\Valid(groups={"Category:Post", "CategoryPut"})
     */
    private $picture;

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

    /**
     * @return Picture|null
     */
    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture|null $picture
     * @return Category
     */
    public function setPicture(?Picture $picture): Category
    {
        $this->picture = $picture;
        if ($picture instanceof Picture) {
            $picture
                ->setCategory($this)
            ;
        }
        return $this;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return Category
     */
    public function setForm(Form $form): Category
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return Form|null
     */
    public function getDraftForm(): ?Form
    {
        return $this->draftForm;
    }

    /**
     * @param Form|null $draftForm
     * @return Category
     */
    public function setDraftForm(?Form $draftForm): Category
    {
        $this->draftForm = $draftForm;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @param bool $online
     * @return Category
     */
    public function setOnline(bool $online): Category
    {
        $this->online = $online;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     * @return Category
     */
    public function setPublic(bool $public): Category
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Category
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}

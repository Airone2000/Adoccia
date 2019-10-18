<?php

namespace App\Entity;

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
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $published = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="categories")
     */
    private $createdBy;

    /**
     * @var Picture|null
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, mappedBy="category")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     *
     * @Assert\Valid(groups={"Category:Post", "CategoryPut"})
     */
    private $picture;

    /**
     * @var Form
     * @ORM\OneToOne(targetEntity="App\Entity\Form", cascade={"persist", "remove"}, mappedBy="category")
     */
    private $form;


    public function __construct()
    {
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

    public function setName(string $name): self
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

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

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
        $picture
            ->setCategory($this)
        ;
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
        $form->setCategory($this);
        return $this;
    }

}

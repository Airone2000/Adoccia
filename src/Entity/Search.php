<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity is a saved instance of a search
 *
 * @ORM\Entity(repositoryClass="App\Repository\SearchRepository")
 */
class Search
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=false)
     */
    private $criterias;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     */
    private $category;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getCriterias(): array
    {
        return (array) $this->criterias;
    }

    /**
     * @param array $criterias
     * @return Search
     */
    public function setCriterias(array $criterias): Search
    {
        $this->criterias = $criterias;
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
     * @return Search
     */
    public function setCategory(Category $category): Search
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Search
     */
    public function setUser(User $user): Search
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Search
     */
    public function setCreatedAt(\DateTime $createdAt): Search
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}

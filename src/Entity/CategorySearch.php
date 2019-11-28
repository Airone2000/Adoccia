<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category_search")
 */
class CategorySearch
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var null|User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $guestUniqueID;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $orderBy;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $filter;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     */
    private $page = 1;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;


    public function __construct()
    {
        $this->page = 1;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param null|int $id
     * @return CategorySearch
     */
    public function setId(?int $id): CategorySearch
    {
        $this->id = $id;
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
     * @return CategorySearch
     */
    public function setUser(?User $user): CategorySearch
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGuestUniqueID(): ?string
    {
        return $this->guestUniqueID;
    }

    /**
     * @param string|null $guestUniqueID
     * @return CategorySearch
     */
    public function setGuestUniqueID(?string $guestUniqueID): CategorySearch
    {
        $this->guestUniqueID = $guestUniqueID;
        return $this;
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
     * @return CategorySearch
     */
    public function setTitle(?string $title): CategorySearch
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @param string|null $orderBy
     * @return CategorySearch
     */
    public function setOrderBy(?string $orderBy): CategorySearch
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilter(): ?string
    {
        return $this->filter;
    }

    /**
     * @param string|null $filter
     * @return CategorySearch
     */
    public function setFilter(?string $filter): CategorySearch
    {
        $this->filter = $filter;
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
     * @return CategorySearch
     */
    public function setCreatedAt(\DateTime $createdAt): CategorySearch
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    /**
     * @param int $page
     * @return CategorySearch
     */
    public function setPage(int $page): CategorySearch
    {
        $this->page = $page;
        return $this;
    }

}
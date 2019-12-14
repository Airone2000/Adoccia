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
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $guestUniqueID;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $orderBy;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $filter;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     */
    private $page = 1;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"default":30})
     */
    private $itemsPerPage = 30;

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

    public function isNew(): bool
    {
        return null === $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGuestUniqueID(): ?string
    {
        return $this->guestUniqueID;
    }

    public function setGuestUniqueID(?string $guestUniqueID): self
    {
        $this->guestUniqueID = $guestUniqueID;

        return $this;
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

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function setFilter(?string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }
}

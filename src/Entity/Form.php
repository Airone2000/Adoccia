<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormRepository")
 * @ORM\EntityListeners("App\EntityListener\FormAreaListener")
 */
class Form
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isInMaintenanceMode = false;

    /**
     * @var Category
     * @ORM\OneToOne(targetEntity="App\Entity\Category", inversedBy="form")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\FormArea", mappedBy="form", cascade={"persist"})
     */
    private $areas;


    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isInMaintenanceMode(): bool
    {
        return $this->isInMaintenanceMode;
    }

    /**
     * @param bool $isInMaintenanceMode
     * @return Form
     */
    public function setIsInMaintenanceMode(bool $isInMaintenanceMode): Form
    {
        $this->isInMaintenanceMode = $isInMaintenanceMode;
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
     * @return Form
     */
    public function setCategory(Category $category): Form
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection
     * By default, it's returned, ordered by position ASC
     */
    public function getAreas(): Collection
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['position' => 'ASC']);
        return $this->areas->matching($criteria);
    }

    /**
     * @param Collection $areas
     * @return Form
     */
    public function setAreas(Collection $areas): Form
    {
        $this->areas = $areas;
        return $this;
    }

    /**
     * @param FormArea $area
     * @return Form
     */
    public function addArea(FormArea $area): Form
    {
        $area->setForm($this);
        $this->areas->add($area);
        return $this;
    }


}

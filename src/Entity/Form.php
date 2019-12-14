<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormRepository")
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\FormArea", mappedBy="form", cascade={"persist"})
     */
    private $areas;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

    /**
     * Clone for draftForm generation.
     */
    public function __clone()
    {
        $this->id = null;
        foreach ($this->areas as $area) {
            $this->getAreas()->removeElement($area);
            $this->addArea(clone $area);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection
     *                    By default, it's returned, ordered by position ASC
     */
    public function getAreas(): Collection
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['position' => 'ASC']);

        return $this->areas->matching($criteria);
    }

    public function setAreas(Collection $areas): self
    {
        $this->areas = $areas;

        return $this;
    }

    public function addArea(FormArea $area): self
    {
        $area->setForm($this);
        $this->areas->add($area);

        return $this;
    }
}

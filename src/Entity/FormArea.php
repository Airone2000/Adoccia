<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormAreaRepository")
 */
class FormArea
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Form
     * @ORM\ManyToOne(targetEntity="App\Entity\Form", inversedBy="areas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $form;

    /**
     * @var float
     * @ORM\Column(type="float", precision=10, scale=2, options={"default":100})
     */
    private $width = 100;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $position;


    public function getId(): ?int
    {
        return $this->id;
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
     * @return FormArea
     */
    public function setForm(Form $form): FormArea
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * @param float $width
     * @return FormArea
     */
    public function setWidth(float $width): FormArea
    {
        $this->width = round($width, 3);
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return FormArea
     */
    public function setPosition(int $position): FormArea
    {
        $this->position = $position;
        return $this;
    }

}

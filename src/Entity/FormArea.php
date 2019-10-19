<?php

namespace App\Entity;

use App\Enum\WidgetTypeEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormAreaRepository")
 * @ORM\EntityListeners("\App\EntityListener\FormAreaListener")
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

    /**
     * @var Widget
     * @ORM\OneToOne(targetEntity="App\Entity\Widget", cascade={"persist", "remove"}, mappedBy="formArea")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $widget;


    public function __construct()
    {
        $this->setWidget(new Widget());
    }

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
     * @return int|null
     */
    public function getPosition(): ?int
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

    /**
     * @return Widget
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }

    /**
     * @param Widget $widget
     * @return FormArea
     */
    public function setWidget(Widget $widget): FormArea
    {
        $this->widget = $widget;
        $widget
            ->setFormArea($this)
            ->setType(WidgetTypeEnum::DEFAULT_TYPE)
        ;
        return $this;
    }

}

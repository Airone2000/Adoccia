<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ValueRepository")
 */
class Value
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Fiche
     * @ORM\ManyToOne(targetEntity="App\Entity\Fiche", inversedBy="values")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     */
    private $fiche;

    /**
     * @var Widget
     * @ORM\ManyToOne(targetEntity="App\Entity\Widget")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     */
    private $widget;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $widgetImmutableId;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeText;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeString;

    /**
     * @var null|int
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $valueOfTypeInt;

    /**
     * @var null|int|float
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $valueOfTypeFloat;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="date", nullable=true)
     */
    private $valueOfTypeDate;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="time", nullable=true)
     */
    private $valueOfTypeTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Fiche
     */
    public function getFiche(): Fiche
    {
        return $this->fiche;
    }

    /**
     * @param Fiche $fiche
     * @return Value
     */
    public function setFiche(Fiche $fiche): Value
    {
        $this->fiche = $fiche;
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
     * @return Value
     */
    public function setWidget(Widget $widget): Value
    {
        $this->widget = $widget;
        $this->widgetImmutableId = $widget->getImmutableId();
        return $this;
    }

    /**
     * @return string
     */
    public function getWidgetImmutableId(): string
    {
        return $this->widgetImmutableId;
    }

    /**
     * @return string|null
     */
    public function getValueOfTypeText(): ?string
    {
        return $this->valueOfTypeText;
    }

    /**
     * @param string|null $valueOfTypeText
     * @return Value
     */
    public function setValueOfTypeText(?string $valueOfTypeText): Value
    {
        $this->valueOfTypeText = $valueOfTypeText;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValueOfTypeString(): ?string
    {
        return $this->valueOfTypeString;
    }

    /**
     * @param string|null $valueOfTypeString
     * @return Value
     */
    public function setValueOfTypeString(?string $valueOfTypeString): Value
    {
        $this->valueOfTypeString = $valueOfTypeString;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getValueOfTypeInt(): ?int
    {
        if ($this->valueOfTypeInt !== null) {
            return (int)$this->valueOfTypeInt;
        }

        return null;
    }

    /**
     * @param int|null $valueOfTypeInt
     * @return Value
     */
    public function setValueOfTypeInt(?int $valueOfTypeInt): Value
    {
        $this->valueOfTypeInt = $valueOfTypeInt;
        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getValueOfTypeFloat()
    {
        if ($this->valueOfTypeFloat !== null) {
            return (float)$this->valueOfTypeFloat;
        }

        return $this->valueOfTypeFloat;
    }

    /**
     * @param float|int|null $valueOfTypeFloat
     * @return Value
     */
    public function setValueOfTypeFloat($valueOfTypeFloat)
    {
        if ($valueOfTypeFloat !== null) {
            $valueOfTypeFloat = (float)$valueOfTypeFloat;
        }

        $this->valueOfTypeFloat = $valueOfTypeFloat;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getValueOfTypeDate(): ?\DateTime
    {
        return $this->valueOfTypeDate;
    }

    /**
     * @param \DateTime|null $valueOfTypeDate
     * @return Value
     */
    public function setValueOfTypeDate(?\DateTime $valueOfTypeDate): Value
    {
        $this->valueOfTypeDate = $valueOfTypeDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getValueOfTypeTime(): ?\DateTime
    {
        return $this->valueOfTypeTime;
    }

    /**
     * @param \DateTime|null $valueOfTypeTime
     * @return Value
     */
    public function setValueOfTypeTime(?\DateTime $valueOfTypeTime): Value
    {
        $this->valueOfTypeTime = $valueOfTypeTime;
        return $this;
    }



}

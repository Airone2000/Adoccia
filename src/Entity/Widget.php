<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WidgetRepository")
 */
class Widget
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=30)
     */
    private $type;

    /**
     * @var FormArea
     * @ORM\OneToOne(targetEntity="App\Entity\FormArea", inversedBy="widget")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $formArea;

    /**
     * @var null|string
     * @ORM\Column(name="inner_text", type="text", nullable=true)
     */
    private $innerTextSetting;

    /**
     * @var int|null
     * @ORM\Column(name="min_length", type="bigint", nullable=true)
     */
    private $minLengthSetting;

    /**
     * @var int|null
     * @ORM\Column(name="max_length", type="bigint", nullable=true)
     */
    private $maxLengthSetting;

    /**
     * @var bool
     * @ORM\Column(name="required", type="boolean", options={"default":0})
     */
    private $requiredSetting = false;


    /**
     * Clone for draftForm generation
     */
    public function __clone()
    {
        $this->id = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Widget
     */
    public function setType(string $type): Widget
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return FormArea
     */
    public function getFormArea(): FormArea
    {
        return $this->formArea;
    }

    /**
     * @param FormArea $formArea
     * @return Widget
     */
    public function setFormArea(FormArea $formArea): Widget
    {
        $this->formArea = $formArea;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInnerTextSetting(): ?string
    {
        return $this->innerTextSetting;
    }

    /**
     * @param string|null $innerTextSetting
     * @return Widget
     */
    public function setInnerTextSetting(?string $innerTextSetting): Widget
    {
        $this->innerTextSetting = $innerTextSetting;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinLengthSetting(): ?int
    {
        return $this->minLengthSetting;
    }

    /**
     * @param int|null $minLengthSetting
     * @return Widget
     */
    public function setMinLengthSetting(?int $minLengthSetting): Widget
    {
        $this->minLengthSetting = $minLengthSetting;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxLengthSetting(): ?int
    {
        return $this->maxLengthSetting;
    }

    /**
     * @param int|null $maxLengthSetting
     * @return Widget
     */
    public function setMaxLengthSetting(?int $maxLengthSetting): Widget
    {
        $this->maxLengthSetting = $maxLengthSetting;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequiredSetting(): bool
    {
        return $this->requiredSetting;
    }

    /**
     * @param bool $requiredSetting
     * @return Widget
     */
    public function setRequiredSetting(bool $requiredSetting): Widget
    {
        $this->requiredSetting = $requiredSetting;
        return $this;
    }


}

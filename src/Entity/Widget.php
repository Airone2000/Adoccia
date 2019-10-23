<?php

namespace App\Entity;

use App\Enum\TextAlignPositionEnum;
use App\Validator\Enum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Type(
     *     type="int",
     *     groups={"Widget:SetSetting"}
     * )
     */
    private $minLengthSetting;

    /**
     * @var int|null
     * @ORM\Column(name="max_length", type="bigint", nullable=true)
     * @Assert\Type(
     *     type="int",
     *     groups={"Widget:SetSetting"}
     * )
     */
    private $maxLengthSetting;

    /**
     * @var bool
     * @ORM\Column(name="required", type="boolean", options={"default":0})
     * @Assert\Type(
     *     type="bool",
     *     groups={"Widget:SetSetting"}
     * )
     */
    private $requiredSetting = false;

    /**
     * @var string|null
     * @ORM\Column(name="text_align", type="string", length=20, nullable=true)
     * @Enum(
     *     enumClass="App\Enum\TextAlignPositionEnum",
     *     groups={"Widget:SetSetting"}
     * )
     */
    private $textAlignSetting;


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
        return (bool) $this->requiredSetting;
    }

    /**
     * @param bool|int $requiredSetting
     * @return Widget
     */
    public function setRequiredSetting($requiredSetting): Widget
    {
        // NULL is allowed because it's falsy
        // See \App\Services\FormHandler\FormHandler::changeFormAreaWidgetType
        $this->requiredSetting = (bool)$requiredSetting;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextAlignSetting(): ?string
    {
        return $this->textAlignSetting;
    }

    /**
     * @param string|null $textAlignSetting
     * @return Widget
     */
    public function setTextAlignSetting(?string $textAlignSetting): Widget
    {
        $this->textAlignSetting = $textAlignSetting;
        return $this;
    }

}

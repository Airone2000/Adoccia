<?php

namespace App\Entity;

use App\Enum\WidgetTypeEnum;
use App\Validator\Color;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormAreaRepository")
 * @ORM\EntityListeners("\App\EntityListener\FormAreaListener")
 */
class FormArea
{
    const
        DEFAULT_BORDER_COLOR = "#000000",
        DEFAULT_BORDER_WIDTH = 0,
        DEFAULT_MARGIN = 0,
        DEFAULT_PADDING = 0,
        DEFAULT_BACKGROUND_COLOR = "#FFFFFF",

        MAX_MARGIN = 50,
        MAX_PADDING = 50,
        MAX_BORDER = 15
    ;

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

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.marginTop.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_MARGIN, maxMessage="FormArea.marginTop.max", groups={"FormArea:SetSettings"})
     */
    private $marginTop;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.marginBottom.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_MARGIN, maxMessage="FormArea.marginBottom.max", groups={"FormArea:SetSettings"})
     */
    private $marginBottom;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.marginLeft.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_MARGIN, maxMessage="FormArea.marginLeft.max", groups={"FormArea:SetSettings"})
     */
    private $marginLeft;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.marginRight.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_MARGIN, maxMessage="FormArea.marginRight.max", groups={"FormArea:SetSettings"})
     */
    private $marginRight;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.paddingTop.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_PADDING, maxMessage="FormArea.paddingTop.max", groups={"FormArea:SetSettings"})
     */
    private $paddingTop;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.paddingBottom.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_PADDING, maxMessage="FormArea.paddingBottom.max", groups={"FormArea:SetSettings"})
     */
    private $paddingBottom;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.paddingLeft.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_PADDING, maxMessage="FormArea.paddingLeft.max", groups={"FormArea:SetSettings"})
     */
    private $paddingLeft;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.paddingRight.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_PADDING, maxMessage="FormArea.paddingRight.max", groups={"FormArea:SetSettings"})
     */
    private $paddingRight;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.borderTopWidth.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_BORDER, maxMessage="FormArea.borderTopWidth.max", groups={"FormArea:SetSettings"})
     */
    private $borderTopWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(message="FormArea.borderTopcolor.color", groups={"FormArea:SetSettings"})
     */
    private $borderTopColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.borderBottomWidth.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_BORDER, maxMessage="FormArea.borderBottomWidth.max", groups={"FormArea:SetSettings"})
     */
    private $borderBottomWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(message="FormArea.borderBottomcolor.color", groups={"FormArea:SetSettings"})
     */
    private $borderBottomColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.borderLeftWidth.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_BORDER, maxMessage="FormArea.borderLeftWidth.max", groups={"FormArea:SetSettings"})
     */
    private $borderLeftWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(message="FormArea.borderLeftcolor.color", groups={"FormArea:SetSettings"})
     */
    private $borderLeftColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="int", message="FormArea.borderRightWidth.type.int", groups={"FormArea:SetSettings"})
     * @Assert\Range(max=FormArea::MAX_BORDER, maxMessage="FormArea.borderRightWidth.max", groups={"FormArea:SetSettings"})
     */
    private $borderRightWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(message="FormArea.borderRightcolor.color", groups={"FormArea:SetSettings"})
     */
    private $borderRightColor;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(message="FormArea.backgroundColor.color", groups={"FormArea:SetSettings"})
     */
    private $backgroundColor;


    public function __construct()
    {
        $this->setWidget(new Widget());
        $this->widget->setType(WidgetTypeEnum::DEFAULT_TYPE);
    }

    /**
     * Clone for draftForm generation
     */
    public function __clone()
    {
        $this->id = null;
        $this->setWidget(clone $this->getWidget());
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
        ;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarginTop(): ?int
    {
        return $this->marginTop ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginTop
     * @return FormArea
     */
    public function setMarginTop($marginTop): FormArea
    {
        $this->marginTop = $marginTop;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarginBottom(): ?int
    {
        return $this->marginBottom ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginBottom
     * @return FormArea
     */
    public function setMarginBottom($marginBottom): FormArea
    {
        $this->marginBottom = $marginBottom;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarginLeft(): ?int
    {
        return $this->marginLeft ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginLeft
     * @return FormArea
     */
    public function setMarginLeft($marginLeft): FormArea
    {
        $this->marginLeft = $marginLeft;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarginRight(): ?int
    {
        return $this->marginRight ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginRight
     * @return FormArea
     */
    public function setMarginRight($marginRight): FormArea
    {
        $this->marginRight = $marginRight;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBorderTopWidth(): ?int
    {
        return $this->borderTopWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderTopWidth
     * @return FormArea
     */
    public function setBorderTopWidth($borderTopWidth): FormArea
    {
        $this->borderTopWidth = $borderTopWidth;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBorderTopColor(): ?string
    {
        return $this->borderTopColor ?? self::DEFAULT_BORDER_COLOR;
    }

    /**
     * @param string|null $borderTopColor
     * @return FormArea
     */
    public function setBorderTopColor(?string $borderTopColor): FormArea
    {
        $this->borderTopColor = $borderTopColor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBorderBottomWidth(): ?int
    {
        return $this->borderBottomWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderBottomWidth
     * @return FormArea
     */
    public function setBorderBottomWidth($borderBottomWidth): FormArea
    {
        $this->borderBottomWidth = $borderBottomWidth;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBorderBottomColor(): ?string
    {
        return $this->borderBottomColor ?? self::DEFAULT_BORDER_COLOR;
    }

    /**
     * @param string|null $borderBottomColor
     * @return FormArea
     */
    public function setBorderBottomColor(?string $borderBottomColor): FormArea
    {
        $this->borderBottomColor = $borderBottomColor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBorderLeftWidth(): ?int
    {
        return $this->borderLeftWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderLeftWidth
     * @return FormArea
     */
    public function setBorderLeftWidth($borderLeftWidth): FormArea
    {
        $this->borderLeftWidth = $borderLeftWidth;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBorderLeftColor(): ?string
    {
        return $this->borderLeftColor ?? self::DEFAULT_BORDER_COLOR;
    }

    /**
     * @param string|null $borderLeftColor
     * @return FormArea
     */
    public function setBorderLeftColor(?string $borderLeftColor): FormArea
    {
        $this->borderLeftColor = $borderLeftColor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBorderRightWidth(): ?int
    {
        return $this->borderRightWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderRightWidth
     * @return FormArea
     */
    public function setBorderRightWidth($borderRightWidth): FormArea
    {
        $this->borderRightWidth = $borderRightWidth;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBorderRightColor(): ?string
    {
        return $this->borderRightColor ?? self::DEFAULT_BORDER_COLOR;
    }

    /**
     * @param string|null $borderRightColor
     * @return FormArea
     */
    public function setBorderRightColor(?string $borderRightColor): FormArea
    {
        $this->borderRightColor = $borderRightColor;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor ?? self::DEFAULT_BACKGROUND_COLOR;
    }

    /**
     * @param string|null $backgroundColor
     * @return FormArea
     */
    public function setBackgroundColor(?string $backgroundColor): FormArea
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaddingTop(): ?int
    {
        return $this->paddingTop ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingTop
     * @return FormArea
     */
    public function setPaddingTop($paddingTop): FormArea
    {
        $this->paddingTop = $paddingTop;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaddingBottom(): ?int
    {
        return $this->paddingBottom ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingBottom
     * @return FormArea
     */
    public function setPaddingBottom($paddingBottom): FormArea
    {
        $this->paddingBottom = $paddingBottom;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaddingLeft(): ?int
    {
        return $this->paddingLeft ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingLeft
     * @return FormArea
     */
    public function setPaddingLeft($paddingLeft): FormArea
    {
        $this->paddingLeft = $paddingLeft;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaddingRight(): ?int
    {
        return $this->paddingRight ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingRight
     * @return FormArea
     */
    public function setPaddingRight($paddingRight): FormArea
    {
        $this->paddingRight = $paddingRight;
        return $this;
    }


}

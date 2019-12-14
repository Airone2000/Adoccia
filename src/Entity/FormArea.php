<?php

namespace App\Entity;

use App\Enum\WidgetTypeEnum;
use App\Enum\WidgetVerticalAlignmentEnum;
use App\Validator\Color;
use App\Validator\Enum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormAreaRepository")
 * @ORM\EntityListeners("\App\EntityListener\FormAreaListener")
 */
class FormArea
{
    const
        DEFAULT_BORDER_COLOR = '#000000';
    const
        DEFAULT_BORDER_WIDTH = 0;
    const
        DEFAULT_MARGIN = 0;
    const
        DEFAULT_PADDING = 0;
    const
        DEFAULT_BACKGROUND_COLOR = '#FFFFFF';
    const
        MAX_MARGIN = 50;
    const
        MAX_PADDING = 50;
    const
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

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Enum(
     *     enumClass="\App\Enum\WidgetVerticalAlignmentEnum",
     *     message="FormArea.widgetVerticalAlignment.enum"
     * )
     */
    private $widgetVerticalAlignment;

    public function __construct()
    {
        $this->setWidget(new Widget());
        $this->widget->setType(WidgetTypeEnum::DEFAULT_TYPE);
    }

    /**
     * Clone for draftForm generation.
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

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = round($width, 3);

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function setWidget(Widget $widget): self
    {
        $this->widget = $widget;
        $widget
            ->setFormArea($this)
        ;

        return $this;
    }

    public function getMarginTop(): ?int
    {
        return $this->marginTop ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginTop
     */
    public function setMarginTop($marginTop): self
    {
        $this->marginTop = $marginTop;

        return $this;
    }

    public function getMarginBottom(): ?int
    {
        return $this->marginBottom ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginBottom
     */
    public function setMarginBottom($marginBottom): self
    {
        $this->marginBottom = $marginBottom;

        return $this;
    }

    public function getMarginLeft(): ?int
    {
        return $this->marginLeft ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginLeft
     */
    public function setMarginLeft($marginLeft): self
    {
        $this->marginLeft = $marginLeft;

        return $this;
    }

    public function getMarginRight(): ?int
    {
        return $this->marginRight ?? self::DEFAULT_MARGIN;
    }

    /**
     * @param int|null $marginRight
     */
    public function setMarginRight($marginRight): self
    {
        $this->marginRight = $marginRight;

        return $this;
    }

    public function getBorderTopWidth(): ?int
    {
        return $this->borderTopWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderTopWidth
     */
    public function setBorderTopWidth($borderTopWidth): self
    {
        $this->borderTopWidth = $borderTopWidth;

        return $this;
    }

    public function getBorderTopColor(): ?string
    {
        return $this->borderTopColor ?? self::DEFAULT_BORDER_COLOR;
    }

    public function setBorderTopColor(?string $borderTopColor): self
    {
        $this->borderTopColor = $borderTopColor;

        return $this;
    }

    public function getBorderBottomWidth(): ?int
    {
        return $this->borderBottomWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderBottomWidth
     */
    public function setBorderBottomWidth($borderBottomWidth): self
    {
        $this->borderBottomWidth = $borderBottomWidth;

        return $this;
    }

    public function getBorderBottomColor(): ?string
    {
        return $this->borderBottomColor ?? self::DEFAULT_BORDER_COLOR;
    }

    public function setBorderBottomColor(?string $borderBottomColor): self
    {
        $this->borderBottomColor = $borderBottomColor;

        return $this;
    }

    public function getBorderLeftWidth(): ?int
    {
        return $this->borderLeftWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderLeftWidth
     */
    public function setBorderLeftWidth($borderLeftWidth): self
    {
        $this->borderLeftWidth = $borderLeftWidth;

        return $this;
    }

    public function getBorderLeftColor(): ?string
    {
        return $this->borderLeftColor ?? self::DEFAULT_BORDER_COLOR;
    }

    public function setBorderLeftColor(?string $borderLeftColor): self
    {
        $this->borderLeftColor = $borderLeftColor;

        return $this;
    }

    public function getBorderRightWidth(): ?int
    {
        return $this->borderRightWidth ?? self::DEFAULT_BORDER_WIDTH;
    }

    /**
     * @param int|null $borderRightWidth
     */
    public function setBorderRightWidth($borderRightWidth): self
    {
        $this->borderRightWidth = $borderRightWidth;

        return $this;
    }

    public function getBorderRightColor(): ?string
    {
        return $this->borderRightColor ?? self::DEFAULT_BORDER_COLOR;
    }

    public function setBorderRightColor(?string $borderRightColor): self
    {
        $this->borderRightColor = $borderRightColor;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor ?? self::DEFAULT_BACKGROUND_COLOR;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getPaddingTop(): ?int
    {
        return $this->paddingTop ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingTop
     */
    public function setPaddingTop($paddingTop): self
    {
        $this->paddingTop = $paddingTop;

        return $this;
    }

    public function getPaddingBottom(): ?int
    {
        return $this->paddingBottom ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingBottom
     */
    public function setPaddingBottom($paddingBottom): self
    {
        $this->paddingBottom = $paddingBottom;

        return $this;
    }

    public function getPaddingLeft(): ?int
    {
        return $this->paddingLeft ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingLeft
     */
    public function setPaddingLeft($paddingLeft): self
    {
        $this->paddingLeft = $paddingLeft;

        return $this;
    }

    public function getPaddingRight(): ?int
    {
        return $this->paddingRight ?? self::DEFAULT_PADDING;
    }

    /**
     * @param int|null $paddingRight
     */
    public function setPaddingRight($paddingRight): self
    {
        $this->paddingRight = $paddingRight;

        return $this;
    }

    public function getWidgetVerticalAlignment(): ?string
    {
        return $this->widgetVerticalAlignment ?? WidgetVerticalAlignmentEnum::START;
    }

    public function setWidgetVerticalAlignment(?string $widgetVerticalAlignment): self
    {
        $this->widgetVerticalAlignment = $widgetVerticalAlignment;

        return $this;
    }
}

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
    const
        DEFAULT_BORDER_COLOR = "#000000",
        DEFAULT_BORDER_WIDTH = 0,
        DEFAULT_MARGIN = 0,
        DEFAULT_PADDING = 0,
        DEFAULT_BACKGROUND_COLOR = "#FFFFFF"
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
     */
    private $marginTop;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $marginBottom;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $marginLeft;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $marginRight;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paddingTop;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paddingBottom;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paddingLeft;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $paddingRight;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $borderTopWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $borderTopColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $borderBottomWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $borderBottomColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $borderLeftWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $borderLeftColor;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $borderRightWidth;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $borderRightColor;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
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
    public function setMarginTop(?int $marginTop): FormArea
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
    public function setMarginBottom(?int $marginBottom): FormArea
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
    public function setMarginLeft(?int $marginLeft): FormArea
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
    public function setMarginRight(?int $marginRight): FormArea
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
    public function setBorderTopWidth(?int $borderTopWidth): FormArea
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
    public function setBorderBottomWidth(?int $borderBottomWidth): FormArea
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
    public function setBorderLeftWidth(?int $borderLeftWidth): FormArea
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
    public function setBorderRightWidth(?int $borderRightWidth): FormArea
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
    public function setPaddingTop(?int $paddingTop): FormArea
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
    public function setPaddingBottom(?int $paddingBottom): FormArea
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
    public function setPaddingLeft(?int $paddingLeft): FormArea
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
    public function setPaddingRight(?int $paddingRight): FormArea
    {
        $this->paddingRight = $paddingRight;
        return $this;
    }


}

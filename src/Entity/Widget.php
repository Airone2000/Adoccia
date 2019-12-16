<?php

namespace App\Entity;

use App\Enum\DateFormatEnum;
use App\Enum\TimeFormatEnum;
use App\Validator\Color;
use App\Validator\Enum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WidgetRepository")
 */
class Widget
{
    const DEFAULT_TEXT_COLOR = '#000000';
    const DEFAULT_DECIMAL_COUNT = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $immutableId;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=30)
     */
    private $type;

    /**
     * @var FormArea
     *               Fetch EAGER for performance in TWIG_widget_types::form_area_start(widget.formArea)
     * @ORM\OneToOne(targetEntity="App\Entity\FormArea", inversedBy="widget", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $formArea;

    /**
     * @var string|null
     * @ORM\Column(name="inner_text", type="text", nullable=true)
     */
    private $innerText;

    /**
     * @var int|null
     * @ORM\Column(name="min_length", type="bigint", nullable=true)
     * @Assert\Type(
     *     type="int",
     *     groups={"StringWidget:SetSetting"}
     * )
     */
    private $minLength;

    /**
     * @var int|null
     * @ORM\Column(name="max_length", type="bigint", nullable=true)
     * @Assert\Type(
     *     type="int",
     *     groups={"StringWidget:SetSetting"}
     * )
     */
    private $maxLength;

    /**
     * @var bool
     * @ORM\Column(name="required", type="boolean", options={"default":0})
     * @Assert\Type(
     *     type="bool",
     *     groups={"StringWidget:SetSetting"}
     * )
     */
    private $required = false;

    /**
     * @var string|null
     * @ORM\Column(name="text_align", type="string", length=20, nullable=true)
     * @Enum(
     *     enumClass="App\Enum\TextAlignPositionEnum",
     *     groups={"LabelWidget:SetSettings"}
     * )
     */
    private $textAlign;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Color(
     *     groups={"LabelWidget:SetSettings"},
     *     message="Widget.textColor.color"
     * )
     */
    private $textColor;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(
     *     max="250",
     *     maxMessage="Widget.inputPlaceholder.length.max"
     * )
     */
    private $inputPlaceholder;

    /**
     * @var int|null
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.min.length.max"
     * )
     */
    private $min;

    /**
     * @var int|null
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.max.length.max"
     * )
     */
    private $max;

    /**
     * @var int|null
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.decimalCount.length.max",
     *     groups={"FloatWidget:SetSettings"}
     * )
     *
     * @Assert\Type(
     *     type="numeric",
     *     groups={"FloatWidget:SetSettings"}
     * )
     */
    private $decimalCount;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Enum(
     *     enumClass="App\Enum\DateFormatEnum",
     *     message="Widget.dateFormat.enum"
     * )
     */
    private $dateFormat;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Enum(
     *     enumClass="App\Enum\TimeFormatEnum",
     *     message="Widget.timeFormat.enum"
     * )
     */
    private $timeFormat;

    /**
     * @var array|null
     * @ORM\Column(type="json", nullable=true)
     */
    private $choices;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $multipleValues = false;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\GreaterThanOrEqual(
     *     value=0,
     *     groups={"MapWidget:SetSettings"}
     * )
     */
    private $minMarkers;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxMarkers;

    public function __construct()
    {
        $this->immutableId = uniqid('e');
    }

    /**
     * Clone for draftForm generation.
     */
    public function __clone()
    {
        $this->id = null;
    }

    public function resetSettings()
    {
        try {
            $omittedProperties = ['id', 'immutableId', 'type', 'formArea'];
            $reflection = new \ReflectionClass($this);
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
                $propertyName = $property->getName();
                if (!\in_array($propertyName, $omittedProperties, true)) {
                    $setter = "set{$propertyName}";
                    if (method_exists($this, $setter)) {
                        \call_user_func([$this, $setter], null); // falsy -> cast internally based on type hint
                    }
                }
            }
        } catch (\ReflectionException $e) { /* The class exists because we are in ! */
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmutableId(): string
    {
        return $this->immutableId;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFormArea(): FormArea
    {
        return $this->formArea;
    }

    public function setFormArea(FormArea $formArea): self
    {
        $this->formArea = $formArea;

        return $this;
    }

    public function getInnerText(): ?string
    {
        return $this->innerText;
    }

    public function setInnerText(?string $innerText): self
    {
        $this->innerText = $innerText;

        return $this;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function setMinLength(?int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function setMaxLength(?int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function isRequired(): bool
    {
        return (bool) $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = (bool) $required;

        return $this;
    }

    public function getTextAlign(): ?string
    {
        return $this->textAlign;
    }

    public function setTextAlign(?string $textAlign): self
    {
        $this->textAlign = $textAlign;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor ?? self::DEFAULT_TEXT_COLOR;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getInputPlaceholder(): ?string
    {
        return $this->inputPlaceholder;
    }

    public function setInputPlaceholder(?string $inputPlaceholder): self
    {
        $this->inputPlaceholder = $inputPlaceholder;

        return $this;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getDecimalCount(): ?int
    {
        return $this->decimalCount;
    }

    public function setDecimalCount(?int $decimalCount): self
    {
        $this->decimalCount = $decimalCount;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat ?? DateFormatEnum::DEFAULT_DATE_FORMAT;
    }

    public function setDateFormat(?string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getTimeFormat(): ?string
    {
        return $this->timeFormat ?? TimeFormatEnum::DEFAULT_TIME_FORMAT;
    }

    public function setTimeFormat(?string $timeFormat): self
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    public function getChoices(): ?array
    {
        return (array) $this->choices;
    }

    public function setChoices(?array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public function getMultipleValues(): bool
    {
        return (bool) $this->multipleValues;
    }

    public function setMultipleValues(?bool $multipleValues): self
    {
        $this->multipleValues = $multipleValues ?? false;

        return $this;
    }

    public function hasMultipleValues(): bool
    {
        return $this->getMultipleValues();
    }

    public function getMinMarkers(): ?int
    {
        return $this->minMarkers;
    }

    public function setMinMarkers(?int $minMarkers): self
    {
        $this->minMarkers = $minMarkers;

        return $this;
    }

    public function getMaxMarkers(): ?int
    {
        return $this->maxMarkers;
    }

    public function setMaxMarkers(?int $maxMarkers): self
    {
        $this->maxMarkers = $maxMarkers;

        return $this;
    }
}

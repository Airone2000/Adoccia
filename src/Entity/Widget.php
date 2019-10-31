<?php

namespace App\Entity;

use App\Enum\DateFormatEnum;
use App\Enum\TextAlignPositionEnum;
use App\Validator\Color;
use App\Validator\Enum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WidgetRepository")
 */
class Widget
{

    const
        DEFAULT_TEXT_COLOR = "#000000",
        DEFAULT_DECIMAL_COUNT = 2
    ;

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
     * @ORM\OneToOne(targetEntity="App\Entity\FormArea", inversedBy="widget")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $formArea;

    /**
     * @var null|string
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
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(
     *     max="250",
     *     maxMessage="Widget.inputPlaceholder.length.max"
     * )
     */
    private $inputPlaceholder;

    /**
     * @var null|int
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.min.length.max"
     * )
     */
    private $min;

    /**
     * @var null|int
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.max.length.max"
     * )
     */
    private $max;

    /**
     * @var null|int
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max="100",
     *     maxMessage="Widget.decimalCount.length.max"
     * )
     */
    private $decimalCount;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Enum(
     *     enumClass="App\Enum\DateFormatEnum",
     *     message="Widget.dateFormat.enum"
     * )
     */
    private $dateFormat;


    public function __construct()
    {
        $this->immutableId = uniqid('e');
    }

    /**
     * Clone for draftForm generation
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
                if (!in_array($propertyName, $omittedProperties)) {
                    $setter = "set{$propertyName}";
                    if (method_exists($this, $setter)) {
                        call_user_func([$this, $setter], null); // falsy -> cast internally based on type hint
                    }
                }
            }
        }
        catch (\ReflectionException $e) { /* The class exists because we are in ! */ }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImmutableId(): string
    {
        return $this->immutableId;
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
    public function getInnerText(): ?string
    {
        return $this->innerText;
    }

    /**
     * @param string|null $innerText
     * @return Widget
     */
    public function setInnerText(?string $innerText): Widget
    {
        $this->innerText = $innerText;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * @param int|null $minLength
     * @return Widget
     */
    public function setMinLength(?int $minLength): Widget
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @param int|null $maxLength
     * @return Widget
     */
    public function setMaxLength(?int $maxLength): Widget
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return (bool)$this->required;
    }

    /**
     * @param bool|null $required
     * @return Widget
     */
    public function setRequired(?bool $required): Widget
    {
        $this->required = (bool)$required;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextAlign(): ?string
    {
        return $this->textAlign;
    }

    /**
     * @param string|null $textAlign
     * @return Widget
     */
    public function setTextAlign(?string $textAlign): Widget
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextColor(): ?string
    {
        return $this->textColor ?? self::DEFAULT_TEXT_COLOR;
    }

    /**
     * @param string|null $textColor
     * @return Widget
     */
    public function setTextColor(?string $textColor): Widget
    {
        $this->textColor = $textColor;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInputPlaceholder(): ?string
    {
        return $this->inputPlaceholder;
    }

    /**
     * @param string|null $inputPlaceholder
     * @return Widget
     */
    public function setInputPlaceholder(?string $inputPlaceholder): Widget
    {
        $this->inputPlaceholder = $inputPlaceholder;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @param int|null $min
     * @return Widget
     */
    public function setMin(?int $min): Widget
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param int|null $max
     * @return Widget
     */
    public function setMax(?int $max): Widget
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDecimalCount(): ?int
    {
        return $this->decimalCount;
    }

    /**
     * @param int|null $decimalCount
     * @return Widget
     */
    public function setDecimalCount(?int $decimalCount): Widget
    {
        $this->decimalCount = $decimalCount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDateFormat(): ?string
    {
        return $this->dateFormat ?? DateFormatEnum::DEFAULT_DATE_FORMAT;
    }

    /**
     * @param string|null $dateFormat
     * @return Widget
     */
    public function setDateFormat(?string $dateFormat): Widget
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

}

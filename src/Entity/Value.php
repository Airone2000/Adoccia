<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ValueRepository")
 */
class Value
{
    const DEFAULT_VALUE_OF_TYPE_BUTTON = [
        'label' => '', 'ilabel' => '',
        'target' => '', 'itarget' => '',
    ];

    const DEFAULT_VALUE_OF_TYPE_MAP = [
        'center' => null,
        'zoom' => null,
        'markers' => [],
    ];

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Widget", fetch="EAGER")
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
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeText;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeString;

    /**
     * @var int|null
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $valueOfTypeInt;

    /**
     * @var int|float|null
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

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeRadio;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valueOfTypeEmail;

    /**
     * @var array|null
     * @ORM\Column(type="json", nullable=true)
     */
    private $valueOfTypeMap;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOfTypeVideo;

    /**
     * @var Picture|null
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $valueOfTypePicture;

    /**
     * JSON_EXTRACT is case-sensitive.
     * Thus, I created a "ilabel" and "itarget" attributes
     * to help search CI.
     *
     * Defined as default value here make sure the search always work for any fiche
     *
     * @var array|null
     * @ORM\Column(type="json", nullable=true)
     */
    private $valueOfTypeButton = self::DEFAULT_VALUE_OF_TYPE_BUTTON;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiche(): Fiche
    {
        return $this->fiche;
    }

    public function setFiche(Fiche $fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function setWidget(Widget $widget): self
    {
        $this->widget = $widget;
        $this->widgetImmutableId = $widget->getImmutableId();

        return $this;
    }

    public function getWidgetImmutableId(): string
    {
        return $this->widgetImmutableId;
    }

    public function getValueOfTypeText(): ?string
    {
        return $this->valueOfTypeText;
    }

    public function setValueOfTypeText(?string $valueOfTypeText): self
    {
        $this->valueOfTypeText = $valueOfTypeText;

        return $this;
    }

    public function getValueOfTypeString(): ?string
    {
        return $this->valueOfTypeString;
    }

    public function setValueOfTypeString(?string $valueOfTypeString): self
    {
        $this->valueOfTypeString = $valueOfTypeString;

        return $this;
    }

    public function getValueOfTypeInt(): ?int
    {
        if (null !== $this->valueOfTypeInt) {
            return (int) $this->valueOfTypeInt;
        }

        return null;
    }

    public function setValueOfTypeInt(?int $valueOfTypeInt): self
    {
        $this->valueOfTypeInt = $valueOfTypeInt;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getValueOfTypeFloat()
    {
        if (null !== $this->valueOfTypeFloat) {
            return (float) $this->valueOfTypeFloat;
        }

        return $this->valueOfTypeFloat;
    }

    /**
     * @param float|int|null $valueOfTypeFloat
     *
     * @return Value
     */
    public function setValueOfTypeFloat($valueOfTypeFloat)
    {
        if (null !== $valueOfTypeFloat) {
            $valueOfTypeFloat = (float) $valueOfTypeFloat;
        }

        $this->valueOfTypeFloat = $valueOfTypeFloat;

        return $this;
    }

    public function getValueOfTypeDate(): ?\DateTime
    {
        return $this->valueOfTypeDate;
    }

    public function setValueOfTypeDate(?\DateTime $valueOfTypeDate): self
    {
        $this->valueOfTypeDate = $valueOfTypeDate;

        return $this;
    }

    public function getValueOfTypeTime(): ?\DateTime
    {
        return $this->valueOfTypeTime;
    }

    public function setValueOfTypeTime(?\DateTime $valueOfTypeTime): self
    {
        $this->valueOfTypeTime = $valueOfTypeTime;

        return $this;
    }

    public function getValueOfTypeRadio(): ?string
    {
        return $this->valueOfTypeRadio;
    }

    /**
     * @param string|array|null $valueOfTypeRadio
     */
    public function setValueOfTypeRadio($valueOfTypeRadio): self
    {
        if (\is_array($valueOfTypeRadio)) {
            $valueOfTypeRadio = implode(',', $valueOfTypeRadio);
        }

        $this->valueOfTypeRadio = $valueOfTypeRadio;

        return $this;
    }

    public function getValueOfTypeButton(): ?array
    {
        return $this->valueOfTypeButton;
    }

    public function setValueOfTypeButton(?array $valueOfTypeButton): self
    {
        if (null === $valueOfTypeButton) {
            $valueOfTypeButton = self::DEFAULT_VALUE_OF_TYPE_BUTTON;
        }

        if (\is_array($valueOfTypeButton)) {
            $label = (string) $valueOfTypeButton['label'];
            $target = (string) $valueOfTypeButton['target'];

            if (empty($target)) {
                $label = '';
            }

            $valueOfTypeButton['label'] = $label;
            $valueOfTypeButton['target'] = $target;

            $label = trim(mb_strtolower($label));
            $target = trim(mb_strtolower($target));

            $valueOfTypeButton = ['ilabel' => $label, 'itarget' => $target] + $valueOfTypeButton;
        }

        $this->valueOfTypeButton = $valueOfTypeButton;

        return $this;
    }

    public function getValueOfTypeEmail(): ?string
    {
        return $this->valueOfTypeEmail;
    }

    public function setValueOfTypeEmail(?string $valueOfTypeEmail): self
    {
        $this->valueOfTypeEmail = $valueOfTypeEmail;

        return $this;
    }

    public function getValueOfTypeMap(): ?array
    {
        return $this->valueOfTypeMap;
    }

    public function setValueOfTypeMap(?array $valueOfTypeMap): self
    {
        if (null === $valueOfTypeMap) {
            $valueOfTypeMap = self::DEFAULT_VALUE_OF_TYPE_MAP;
        }

        $this->valueOfTypeMap = $valueOfTypeMap;

        return $this;
    }

    public function getValueOfTypeVideo(): ?string
    {
        return $this->valueOfTypeVideo;
    }

    public function setValueOfTypeVideo(?string $valueOfTypeVideo): self
    {
        $this->valueOfTypeVideo = $valueOfTypeVideo;

        return $this;
    }

    public function getValueOfTypePicture(): ?Picture
    {
        return $this->valueOfTypePicture;
    }

    public function setValueOfTypePicture(?Picture $valueOfTypePicture): self
    {
        $this->valueOfTypePicture = $valueOfTypePicture;
        if ($valueOfTypePicture instanceof Picture) {
            $valueOfTypePicture->setIsTemp(false);
        }

        return $this;
    }
}

<?php

namespace App\Services\FormHandler;

use App\Annotation\WidgetSettable;
use App\Entity\Form;
use App\Entity\FormArea;
use App\Entity\Widget;
use App\Enum\WidgetTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class FormHandler implements FormHandlerInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PropertyAccess
     */
    private $propertyAccess;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccess)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->propertyAccess = $propertyAccess;
    }

    public function setFormAreaSize(FormArea $formArea, $size): void
    {
        $areaMinSize = +$this->parameterBag->get('areaMinSize');
        $areaMaxSize = +$this->parameterBag->get('areaMaxSize');

        if ($size >= $areaMinSize && $size <= $areaMaxSize) {
            $formArea->setWidth($size);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Form $form
     * @param array $mapPositionToAreaId
     * @throws \Exception
     */
    public function sortForm(Form $form, array $mapPositionToAreaId): void
    {
        $sizesToConsume = range(1, $form->getAreas()->count());
        $sizesToConsume = array_flip($sizesToConsume);

        /** @var FormArea $area */
        foreach ($form->getAreas() as $area) {
            # Position is given for this area is this position is not already used
            if (isset($mapPositionToAreaId[$area->getId()]) && isset($sizesToConsume[$mapPositionToAreaId[$area->getId()]])) {
                $position = $mapPositionToAreaId[$area->getId()];
                $area->setPosition($position);
                unset($sizesToConsume[$position]);
            }
            else {
                throw new \Exception('Unexpected error when sorting due to foreign area or already used position.');
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param Widget $widget
     * @param string|null $newType
     * @throws \Exception
     */
    public function changeFormAreaWidgetType(Widget $widget, ?string $newType): void
    {
        if (WidgetTypeEnum::isset($newType)) {
            $widget->setType($newType);
            $this->entityManager->flush();
            return;
        }

        throw new \Exception('Something wrong happen when trying to set new type on widget.');
    }

    /**
     * @param Widget $widget
     * @param string|null $attribute
     * @param $value
     * @throws \Exception
     */
    public function setWidgetSetting(Widget $widget, ?string $attribute, $value): void
    {
        $attribute = "{$attribute}Setting";
        if ($this->propertyAccess->isWritable($widget, $attribute)) {
            try {
                $this->propertyAccess->setValue($widget, $attribute, $value);
                $this->entityManager->flush();
            }
            catch (\Exception $e) {
                throw new \Exception('Error while setting value for ' . $attribute);
            }
        }
        else {
            throw new \Exception('Non settable setting');
        }

    }
}
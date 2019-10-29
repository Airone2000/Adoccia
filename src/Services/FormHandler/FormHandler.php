<?php

namespace App\Services\FormHandler;

use App\Entity\Category;
use App\Entity\Form;
use App\Entity\FormArea;
use App\Entity\Value;
use App\Entity\Widget;
use App\Enum\WidgetTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ParameterBagInterface $parameterBag,
                                EntityManagerInterface $entityManager,
                                PropertyAccessorInterface $propertyAccess,
                                ValidatorInterface $validator)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->propertyAccess = $propertyAccess;
        $this->validator = $validator;
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

            $oldType = $widget->getType();

            $reflectionClass = new \ReflectionClass($widget);
            $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);
            /** @var \ReflectionProperty $property */
            foreach ($properties as $property) {
                $propertyName = ucfirst($property->name);
                $endWithSetting = (substr($propertyName, - 7) === 'Setting');
                if ($endWithSetting) {
                    $setter = "set{$propertyName}";
                    if (method_exists($widget, $setter)) {
                        call_user_func([$widget, $setter], null); // falsy
                    }
                }
            }

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

                $violations = $this->validator->validate($widget, null, ['Widget:SetSetting']);
                if (count($violations) > 0) {
                    throw new \Exception('Wrong value for setting ' . $attribute);
                }

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

    /**
     * @param Category $category
     * @param bool $overwrite
     */
    public function setDraftForm(Category $category, bool $overwrite = false): void
    {
        if ($category->getDraftForm() !== null) {
            if (!$overwrite) return;
        }

        $draftForm = clone $category->getForm();
        $category->setDraftForm($draftForm);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @throws \Exception
     */
    public function publishDraftForm(Category $category): void
    {
        try {
            if (($draftForm = $category->getDraftForm()) instanceof Form) {

                # First, reAffect value to the good widget
                $valueRepository = $this->entityManager->getRepository(Value::class);
                $valueRepository->reAffectValueToWidget($draftForm);

                # Remove the old form
                if ($form = $category->getForm()) {
                    $this->entityManager->remove($form);
                }

                # Replace the old by the new and set draft as null
                $category
                    ->setForm($draftForm)
                    ->setDraftForm(null);

                $this->entityManager->flush();
            }
        }
        catch (\Exception $e) {
            throw new \Exception('An error occurred when trying to publish the draft form.');
        }
    }

    /**
     * @param Form $form
     * @return FormArea
     */
    public function addFormAreaToDraftForm(Form $form): FormArea
    {
        $form->addArea($formArea = new FormArea());
        $this->entityManager->flush();

        /**
         * Nous ajoutons une area au formulaire.
         * Cette area contient par défaut un widget.
         * Toutes les fiches de la catégorie doivent recevoir une nouvelle valeur associée
         * à ce nouveau widget.
         *
         * De cette manière, la recherche avancée permet de les retrouver.
         */

        $widget = $formArea->getWidget();
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['draftForm' => $form]);
        $valueRepository = $this->entityManager->getRepository(Value::class);
        $valueRepository->addWidgetValueToCategoryFiches($category, $widget);

        return $formArea;
    }

}
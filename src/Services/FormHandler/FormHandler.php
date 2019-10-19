<?php

namespace App\Services\FormHandler;

use App\Entity\Form;
use App\Entity\FormArea;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
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
}
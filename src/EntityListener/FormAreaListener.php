<?php

namespace App\EntityListener;

use App\Entity\Form;
use App\Entity\FormArea;
use App\Repository\FormAreaRepository;
use Doctrine\ORM\EntityManagerInterface;

final class FormAreaListener
{
    /**
     * @var FormAreaRepository
     */
    private $formAreaRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(FormAreaRepository $formAreaRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->formAreaRepository = $formAreaRepository;
        $this->entityManager = $entityManager;
    }

    function prePersist(FormArea $formArea): void
    {
        /**
         * If position is NULL, then fetch lastPositionedArea from database and add +1
         */
        if ($formArea->getPosition() === null && $formArea->getForm() instanceof Form) {
            $lastPositionedArea = $this->formAreaRepository->getLastPositionedFormArea($formArea->getForm());
            if ($lastPositionedArea instanceof FormArea) {
                $formArea->setPosition($lastPositionedArea->getPosition() + 1);
            }
            else {
                $formArea->setPosition(1);
            }
        }
    }

    function postRemove(FormArea $formArea): void
    {

        /**
         * When an item is deleted, it lefts an hole in the set of areas.
         * Example :
         * Area 1
         * Area 2
         * Area 3 <- deleted
         * Area 4
         *
         * There'is no longer Area with position 3. Thus, we should recompute position for each areas
         * so that Area4 becomes Area3
         */

        /** @var FormArea[] $areas */
        $areas = $this->formAreaRepository->findBy(['form' => $formArea->getForm()], ['position' => 'ASC']);
        $position = 1;
        foreach ($areas as $area) {
            $area->setPosition($position++);
        }
        $this->entityManager->flush();
    }
}
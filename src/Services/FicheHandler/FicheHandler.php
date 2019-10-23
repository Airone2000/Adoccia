<?php

namespace App\Services\FicheHandler;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\Value;
use App\Form\FicheType;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FicheHandler implements FicheHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var WidgetRepository
     */
    private $widgetRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
                                WidgetRepository $widgetRepository,
                                ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->widgetRepository = $widgetRepository;
        $this->validator = $validator;
    }

    public function createFicheFromFicheTypeData(array $data): Fiche
    {
        /**
         * We assume are incoming data is the result of FicheType submission.
         * This way, we assume constraints did their job and the incoming array of data
         * is trustable.
         */

        $fiche = new Fiche();

        $fiche
            ->setTitle($data['title'])
        ;

        # Link category
        if (isset($data['category']) && ($category = $data['category']) instanceof Category) {
            $fiche->setCategory($category);
        }

        $widgetIds = array_keys($data);
        $widgets = $this->widgetRepository->findBy(['id' => $widgetIds]);

        foreach ($widgets as $widget) {
            $value = new Value();
            $value->setWidget($widget);
            $fiche->addValue($value);
        }

        # Additional check to make sure everything is fine
        $errors = $this->validator->validate($fiche);
        if (count($errors) > 0) {
            throw new \LogicException("Fiche is not valid according to the validator in \App\Services\FicheHandler\FicheHandler.");
        }

        $this->entityManager->persist($fiche);
        $this->entityManager->flush();

        return $fiche;
    }
}
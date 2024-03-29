<?php

namespace App\Services\FicheHandler;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\Value;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

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
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager,
                                WidgetRepository $widgetRepository,
                                ValidatorInterface $validator,
                                FormFactoryInterface $formFactory,
                                Environment $twig,
                                TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->widgetRepository = $widgetRepository;
        $this->validator = $validator;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws \Exception
     */
    public function editFicheFromFicheTypeData(Fiche $fiche, array $data): Fiche
    {
        return $this->createFicheFromFicheTypeData($data, $fiche);
    }

    /**
     * @throws \Exception
     */
    public function createFicheFromFicheTypeData(array $data, ?Fiche $fiche = null): Fiche
    {
        /**
         * We assume are incoming data is the result of FicheType submission.
         * This way, we assume constraints did their job and the incoming array of data
         * is trustable.
         */
        $fiche = $fiche ?? new Fiche();

        /*
         * Delete all value for this fiche is already existing
         * since dataFromForm gives use all once again
         */
        if ($fiche->getId()) {
            $this->entityManager->getRepository(Value::class)->deleteByFiche($fiche);
        }

        $fiche
            ->setTitle($data['title'])
            ->setPublished((bool) ($data['published'] ?? null))
            ->setCreator($this->tokenStorage->getToken()->getUser())
            ->setPicture($data['picture'] ?? null)
        ;

        // Link category
        if (isset($data['category']) && ($category = $data['category']) instanceof Category) {
            $fiche->setCategory($category);
        }

        $widgetIds = array_keys($data);
        $widgets = $this->widgetRepository->findBy(['id' => $widgetIds]);

        foreach ($widgets as $widget) {
            $datum = $data[$widget->getId()];
            $setter = "setValueOfType{$widget->getType()}";

            $value = new Value();
            if (method_exists(Value::class, $setter)) {
                \call_user_func([$value, $setter], $datum);
            }

            $value->setWidget($widget);
            $fiche->addValue($value);
        }

        // Additional check to make sure everything is fine
        $errors = $this->validator->validate($fiche);
        if (\count($errors) > 0) {
            throw new \LogicException("Fiche is not valid according to the validator in \App\Services\FicheHandler\FicheHandler.");
        }

        if (null === $fiche->getId()) {
            $this->entityManager->persist($fiche);
        }
        $this->entityManager->flush();

        return $fiche;
    }

    public function mapValueToWidgetId(Fiche $fiche): array
    {
        $formData = [];

        /** @var Value $value */
        foreach ($fiche->getValues() as $value) {
            $getter = 'getValueOfType'.$value->getWidget()->getType();
            if (method_exists($value, $getter)) {
                $formData[$value->getWidget()->getId()] = \call_user_func([$value, $getter]);
            }
        }

        return $formData;
    }

    public function getFicheView(Fiche $fiche): string
    {
        $formData = $this->mapValueToWidgetId($fiche);

        $form = $this->formFactory->create(FicheType::class, $formData, [
            'category' => $fiche->getCategory(),
            'attr' => ['readonly' => 'readonly'], // <- prevent from modifying input
            'mode' => FicheModeEnum::DISPLAY,
            'fiche' => $fiche,
        ]);

        $template = $this->twig->render('fiche/_fiche.html.twig', [
            'form' => $form->createView(),
        ]);

        return $template;
    }

    /**
     * This method is time consuming depending on how many fiches the Category contains.
     * It's better to run it asynchronously (RabbitMQ).
     *
     * It's role is to check each fiche of the Category and give an unPublish / invalid status.
     */
    public function unPublishInvalidFiches(Category $category): void
    {
        /** @var Fiche $fiche */
        foreach ($category->getFiches() as $fiche) {
            $ficheData = $this->mapValueToWidgetId($fiche);
            $ficheData['title'] = $fiche->getTitle();

            $form = $this->formFactory->create(FicheType::class, null, [
                'category' => $category,
                'csrf_protection' => false,
            ]);
            $form->submit($ficheData);

            if (!$form->isValid()) {
                $fiche
                    ->setPublished(false)
                    ->setValid(false)
                ;
                $this->entityManager->flush();
            }
        }
    }
}

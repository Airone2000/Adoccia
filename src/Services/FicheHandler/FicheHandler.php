<?php

namespace App\Services\FicheHandler;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\FormArea;
use App\Entity\Value;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
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

    public function __construct(EntityManagerInterface $entityManager,
                                WidgetRepository $widgetRepository,
                                ValidatorInterface $validator,
                                FormFactoryInterface $formFactory,
                                Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->widgetRepository = $widgetRepository;
        $this->validator = $validator;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @param array $data
     * @return Fiche
     * @throws \Exception
     */
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
            $datum = $data[$widget->getId()];
            $setter = "setValueOfType{$widget->getType()}";

            if ($datum !== null && method_exists(Value::class, $setter)) {
                $value = new Value();
                if(call_user_func([$value, $setter], $datum) === false) {
                    throw new \Exception("Unable to set value \"{$datum}\" of type \"{$widget->getType()}\".");
                }
                else {
                    $value->setWidget($widget);
                    $fiche->addValue($value);
                }
            }
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

    public function getFicheView(Fiche $fiche): string
    {
        $formData = [];

        /** @var Value $value */
        foreach ($fiche->getValues() as $value) {
            $getter = "getValueOfType" . $value->getWidget()->getType();
            if (method_exists($value, $getter)) {
                $formData[$value->getWidget()->getId()] = call_user_func([$value, $getter]);
            }
        }

        $form = $this->formFactory->create(FicheType::class, $formData, [
            'category' => $fiche->getCategory(),
            'attr' => ['readonly' => 'readonly'], # <- prevent from modifying input
            'mode' => FicheModeEnum::DISPLAY
        ]);

        $template = $this->twig->render('fiche/_fiche.html.twig', [
            'form' => $form->createView(),
            'modeEditable' => true
        ]);

        return $template;
    }
}
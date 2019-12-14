<?php

namespace App\Form\FormBuilder;

use App\Entity\Widget;
use App\Enum\FicheModeEnum;
use App\Form\FormBuilderType\VideoType;
use App\Services\VideoHandler\VideoHandler;
use App\Validator\VideoURL;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoBuilder implements FormBuilderInterface
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];
        $mode = $options['mode'];

        $builder->add($widget->getId(), VideoType::class, [
            'widget' => $widget,
            'mode' => $mode,
            'empty_data' => null,
            'attr' => [
                'placeholder' => $widget->getInputPlaceholder(),
            ],
            'constraints' => $this->getConstraints($widget),
            'required' => $widget->isRequired(),
            'compound' => false,
        ]);

        if (FicheModeEnum::DISPLAY === $mode) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $preSetDataEvent) use ($widget) {
                $data = $preSetDataEvent->getData();
                $videoURL = (string) $data[$widget->getId()];
                $transformedURL = VideoHandler::transformToReadableVideoURL($videoURL);
                $data[$widget->getId()] = $transformedURL;
                $preSetDataEvent->setData($data);
            });
        }
    }

    protected function getConstraints(Widget $widget): array
    {
        $constraints = [];

        if ($widget->isRequired()) {
            $constraints[] = new NotBlank(['allowNull' => true]);
        }

        $constraints[] = new VideoURL();

        return $constraints;
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\VideoType::class, [
            'widget' => $widget,
        ]);
    }
}

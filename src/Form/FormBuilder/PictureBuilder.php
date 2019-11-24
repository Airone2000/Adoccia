<?php

namespace App\Form\FormBuilder;

use App\Entity\Picture;
use App\Entity\Widget;
use App\Form\FormBuilderType\PictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureBuilder implements FormBuilderInterface
{
    private $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var Widget $widget */
        $widget = $options['widget'];

        $builder->add($widget->getId(), PictureType::class, [
            'widget' => $widget,
            'mode' => $options['mode']
        ]);
        
        $this->setDefaultValueForPictureCoords($builder, $widget);
    }

    private function setDefaultValueForPictureCoords(\Symfony\Component\Form\FormBuilderInterface $builder, Widget $widget): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(PostSubmitEvent $postSubmitEvent) use ($widget){
            $picture = $postSubmitEvent->getData()[$widget->getId()];
            if($picture instanceof Picture) {
                if ($picture->getCropCoords() === null) {
                    if ($picture->getUploadedFile() instanceof UploadedFile && $picture->getCropCoords() === null) {
                        $sizes = getimagesize($picture->getUploadedFile());
                        $defaultCropCoords = [
                            'width' => $sizes[0],
                            'height' => $sizes[1],
                            'x' => 0,
                            'y' => 0
                        ];
                        $picture->setCropCoords($defaultCropCoords);
                    }
                }
            }
        });
    }

    public function buildSearchForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        /* @var \App\Entity\Widget $widget */
        $widget = $options['widget'];
        $builder->add($widget->getImmutableId(), \App\Form\SearchType\PictureType::class, [
            'widget' => $widget
        ]);
    }
}
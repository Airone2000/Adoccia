<?php

namespace App\Form;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('btnOpenPictureUploader', ButtonType::class, [
                'label' => $options['label'],
                'attr' => [
                    'type' => 'button',
                    'class' => 'openPictureUploader'
                ]
            ])
            ->add('pictureId', HiddenType::class)
        ;

        $builder
            ->addModelTransformer(new CallbackTransformer(
                function($value){
                    $data = ['pictureId' => null];
                    if ($value instanceof Picture) {
                        $data['pictureId'] = $value->getId();
                    }
                    return $data;
                },
                function($value){
                    $pictureId = $value['pictureId'];
                    if (is_numeric($pictureId)) {
                        $picture = $this->entityManager->find(Picture::class, $pictureId);
                        return $picture;
                    }
                    return null;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('label', 'Browse ...');
    }
}
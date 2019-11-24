<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
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
        /* @var Category $category */
        $category = $options['data'];

        $builder
            ->add('picture', AdvancedPictureType::class, [
                'validation_groups' => $options['validation_groups'],
                'required' => false
            ])
            ->add('name', null)
            ->add('description')
            ->add('online')
            ->add('public')
        ;

        $builder->get('picture')->addModelTransformer(new CallbackTransformer(
            function($value){return $value;},
            function($value){
                if ($value instanceof Picture) {
                    if ($value->isAutoDelete()){
                        if ($value->getId() !== null) {
                            /* @var EntityManagerInterface $em */
                            $em = $this->entityManager;
                            $em->getRepository(Picture::class)->deletePicture($value);
                        }
                        return null;
                    }

                    if ($value->getUploadedFile() === null) {
                        return null;
                    }
                }
                return $value;
            }
        ));

        $this->setDefaultValueForPictureCoords($builder);
    }

    private function setDefaultValueForPictureCoords(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(PostSubmitEvent $postSubmitEvent){
            /* @var Category $category */
            $category = $postSubmitEvent->getData();
            $picture = $category->getPicture();

            if($picture instanceof Picture) {
                if ($picture->getCropCoords() === null) {
                    if ($picture->getUploadedFile() instanceof UploadedFile && $picture->getCropCoords() === null) {
                        $sizes = getimagesize($picture->getUploadedFile());
                        $size = min($sizes[0], $sizes[1]);
                        $x = ($sizes[0] - $size) / 2;
                        $y = ($sizes[1] - $size) / 2;
                        $defaultCropCoords = [
                            'width' => $size,
                            'height' => $size,
                            'x' => $x,
                            'y' => $y
                        ];
                        $picture->setCropCoords($defaultCropCoords);
                    }
                }
            }

            $postSubmitEvent->setData($category);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

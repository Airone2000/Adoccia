<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Picture;
use App\Validator\CategoryPicture;
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
            ->add('name', null, [
                'required' => false
            ])
            ->add('picture', PictureType::class, [
                'originalPicture' => $category->getPicture(),
                'uniqueId' => uniqid('uid_')
            ])
            ->add('description')
            ->add('online')
            ->add('public')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

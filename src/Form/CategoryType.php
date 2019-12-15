<?php

namespace App\Form;

use App\Entity\Category;
use App\Enum\PictureShapeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'required' => false,
                'label' => 'category.type.name.label',
            ])
            ->add('picture', PictureType::class, [
                'label' => 'category.type.browse.label',
                'originalPicture' => $category->getPicture(),
                'uniqueId' => uniqid('uid_'),
                'cropShape' => PictureShapeEnum::SQUARE,
                'liipImagineFilter' => 'category_picture_thumbnail',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'category.type.description.label'
            ])
            ->add('online', CheckboxType::class, [
                'label' => 'category.type.online.label',
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'category.type.public.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

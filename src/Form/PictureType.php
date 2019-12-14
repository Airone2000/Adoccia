<?php

namespace App\Form;

use App\Entity\Picture;
use App\Enum\PictureShapeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
                    'class' => 'openPictureUploader',
                    'data-unique-id' => $options['uniqueId'],
                    'data-crop-shape' => $options['cropShape'],
                ],
            ])
            ->add('pictureId', HiddenType::class, [
                'attr' => [
                    'data-unique-id' => $options['uniqueId'],
                    'class' => 'input-id',
                ],
            ])
            ->add('pictureURL', HiddenType::class, [
                'attr' => [
                    'data-unique-id' => $options['uniqueId'],
                    'class' => 'picture-url',
                ],
            ])
        ;

        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    $data = ['pictureId' => null];
                    if ($value instanceof Picture) {
                        $data['pictureId'] = $value->getUniqueId();
                    }

                    return $data;
                },
                function ($value) use ($options) {
                    $pictureId = $value['pictureId'];
                    if (is_scalar($pictureId)) {
                        $picture = $this->getPicture($pictureId);
                        if (null === $picture) {
                            return $options['originalPicture'];
                        }

                        return $picture;
                    }

                    return null;
                }
            ));
    }

    private function getPicture($pictureId): ?Picture
    {
        $picture = $this->entityManager->getRepository(Picture::class)->findOneBy([
            'uniqueId' => $pictureId,
            'isTemp' => true,
        ]);

        return $picture;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['uniqueId'] = $options['uniqueId'];
        $view->vars['picture'] = $options['originalPicture'];
        $view->vars['deletable'] = $options['deletable'];
        $view->vars['liipImagineFilter'] = $options['liipImagineFilter'];

        // When I submit a wrong form, I want the previously selected picture
        // to be displayed again (only visual)
        if (isset($view->vars['value']['pictureURL'])) {
            $pictureURL = $view->vars['value']['pictureURL'] ?? null;
            $picture = new Picture();
            $picture->setFilename(basename($pictureURL));
            $view->vars['picture'] = $picture;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('label', 'Browse ...');
        $resolver->setDefault('deletable', true);
        $resolver->setRequired('originalPicture');
        $resolver->setRequired('uniqueId');
        $resolver->setDefault('liipImagineFilter', null);
        $resolver->setDefault('cropShape', PictureShapeEnum::FREE);
        $resolver->setAllowedValues('cropShape', PictureShapeEnum::toArray());
    }

    public function getBlockPrefix()
    {
        return 'custom_picture';
    }
}

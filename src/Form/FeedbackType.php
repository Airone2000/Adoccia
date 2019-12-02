<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Enum\FeedbackTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    FeedbackTypeEnum::FEEDBACK_TYPE_BUG,
                    FeedbackTypeEnum::FEEDBACK_TYPE_FEATURE,
                    FeedbackTypeEnum::FEEDBACK_TYPE_ELSE,
                ],
                'help' => 'What kind of feedback is it?',
            ])
            ->add('description', TextareaType::class, [
                'label' => '',
                'attr' => [
                    'placeholder' => "Please explain what you're suggesting.",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}

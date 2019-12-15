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
                'label' => 'feedback.form.type.label',
                'choices' => [
                    'feedback.form.choice.bug' => FeedbackTypeEnum::FEEDBACK_TYPE_BUG,
                    'feedback.form.choice.feature' => FeedbackTypeEnum::FEEDBACK_TYPE_FEATURE,
                    'feedback.form.choice.else' => FeedbackTypeEnum::FEEDBACK_TYPE_ELSE,
                ],
                'help' => 'feedback.form.type.help',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'feedback.form.description.label',
                'attr' => [
                    'placeholder' => "feedback.form.description.placeholder",
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

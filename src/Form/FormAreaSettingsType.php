<?php

namespace App\Form;

use App\Entity\FormArea;
use App\Enum\WidgetVerticalAlignmentEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormAreaSettingsType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $marginConstraintsAttr = ['attr' => ['min' => 0, 'max' => FormArea::MAX_MARGIN]];
        $paddingConstraintsAttr = ['attr' => ['min' => 0, 'max' => FormArea::MAX_PADDING]];
        $borderConstraintsAttr = ['attr' => ['min' => 0, 'max' => FormArea::MAX_BORDER]];

        $builder
            ->add('marginTop', null, $marginConstraintsAttr)
            ->add('marginBottom', null, $marginConstraintsAttr)
            ->add('marginLeft', null, $marginConstraintsAttr)
            ->add('marginRight', null, $marginConstraintsAttr)
            ->add('paddingTop', null, $paddingConstraintsAttr)
            ->add('paddingBottom', null, $paddingConstraintsAttr)
            ->add('paddingLeft', null, $paddingConstraintsAttr)
            ->add('paddingRight', null, $paddingConstraintsAttr)
            ->add('borderTopWidth', null, $borderConstraintsAttr)
            ->add('borderBottomWidth', null, $borderConstraintsAttr)
            ->add('borderLeftWidth', null, $borderConstraintsAttr)
            ->add('borderRightWidth', null, $borderConstraintsAttr)
            ->add('borderTopColor')
            ->add('borderBottomColor')
            ->add('borderLeftColor')
            ->add('borderRightColor')
            ->add('backgroundColor')
            ->add('widgetVerticalAlignment', ChoiceType::class, [
                'choices' => WidgetVerticalAlignmentEnum::toArray(),
                'choice_label' => function(string $label){
                    return $this->translator->trans("key.{$label}");
                }
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['id'] = 'FormArea_SettingsForm';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormArea::class,
            'validation_groups' => ['FormArea:SetSettings']
        ]);
    }
}

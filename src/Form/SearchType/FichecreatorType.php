<?php

namespace App\Form\SearchType;

use App\Entity\Category;
use App\Enum\SearchCriteriaEnum;
use App\Repository\FicheRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FichecreatorType extends AbstractSearchType
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    public function getBlockPrefix()
    {
        return 'fichit_fiche_creator';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('criteria', ChoiceType::class, [
                'choices' => $this->getSearchCriterias(),
                'choice_label' => function($value) {return "trans.{$value}";},
                'choice_attr' => function(string $value) {
                    $attr = [];
                    switch ($value) {
                        case SearchCriteriaEnum::CREATOR_IS:
                        case SearchCriteriaEnum::CREATOR_IS_NOT:
                            $attr['data-inputs'] = '.value';
                            break;
                    }
                    return $attr;
                }
            ])
            ->add('value', ChoiceType::class, [
                'required' => false,
                'choices' => $this->getAllFicheCreatorOfThisCategory($options['category']),
                'attr' => [
                    'class' => 'value hidden'
                ],
                'multiple' => true
            ])
        ;
    }

    private function getSearchCriterias(): array
    {
        return [
            SearchCriteriaEnum::DISABLED,
            SearchCriteriaEnum::CREATOR_IS,
            SearchCriteriaEnum::CREATOR_IS_NOT
        ];
    }

    private function getAllFicheCreatorOfThisCategory(Category $category): array
    {
        $creators = $this->ficheRepository->getCreatorsForCategory($category);
        $creators = array_map(function($creator){
            return $creator['username'];
        }, $creators);
        return array_flip($creators);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('category');
        $resolver->setAllowedTypes('category', Category::class);
    }
}
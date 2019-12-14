<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\EntityExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PasswordResetPromptEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new EntityExists(['class' => User::class, 'field' => 'email', 'message' => 'There is no user with this email.']),
                ],
            ])
        ;
    }
}

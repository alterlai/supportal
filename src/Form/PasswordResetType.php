<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("old_password", PasswordType::class)
            ->add("new_password", PasswordType::class)
            ->add("new_password_confirm", PasswordType::class)
            ->add("submit", SubmitType::class)
        ;
    }
}
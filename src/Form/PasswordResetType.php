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
            ->add("old_password", PasswordType::class, [
                'label' => 'Huidige wachtwoord'
            ])
            ->add("new_password", PasswordType::class, [
                'label' => 'Nieuw wachtwoord'
            ])
            ->add("new_password_confirm", PasswordType::class, [
                'label' => 'Bevestig nieuw wachtwoord'
            ])
            ->add("submit", SubmitType::class)
        ;
    }
}
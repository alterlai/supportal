<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("from", DateType::class, [
                'widget' => "single_text"
            ])
            ->add("to", DateType::class, [
                'widget' => "single_text"
            ])
            ->add("grouping", ChoiceType::class, [
                'choices' => [
                    'Per gebruiker' => "user",
                    'Per organisatie' => "org"
                ],
                'label' => 'Gegroepeerd:',
                'required' => true,
                'expanded' => true
            ])
            ->add("submit", SubmitType::class)
        ;
    }
}
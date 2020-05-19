<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("fileContent", FileType::class)
            ->add("location", EntityType::class, [
                'class' => Location::class
            ])
            ->add("building", EntityType::class, [
                'class' => Building::class
            ])
            ->add("floor", TextType::class)
            ->add("area", EntityType::class, [
                'class' => Area::class
                ])
            ->add("discipline", EntityType::class, [
                'class' => Discipline::class
            ])
            ->add("documentType", EntityType::class, [
                'class' => \App\Entity\DocumentType::class
            ])
            ->add("description", TextareaType::class)
            ->add("submit", SubmitType::class)
        ;
    }
}
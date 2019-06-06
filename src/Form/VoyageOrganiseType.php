<?php

namespace App\Form;

use App\Entity\VoyageOrganise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoyageOrganiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference')
            ->add('title')
            ->add('description')
            ->add('destination')
            ->add('visible')
            ->add('Category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VoyageOrganise::class,
        ]);
    }
}

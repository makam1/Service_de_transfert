<?php

namespace App\Form;

use App\Entity\Commission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commission::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomenvoyeur')
            ->add('prenomenvoyeur')
            ->add('telephoneenvoyeur')
            ->add('ncienvoyeur')
            ->add('nombeneficiaire')
            ->add('prenombeneficiaire')
            ->add('telephonebeneficiaire')
            ->add('ncibeneficiaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}

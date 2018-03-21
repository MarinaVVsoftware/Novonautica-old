<?php

namespace AppBundle\Form\Tienda;


use AppBundle\Form\PagoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TiendaRegistraPagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
        public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('pagos',CollectionType::class,[
                'entry_type' => PagoType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Solicitud'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_solicitud_pagos';
    }
}
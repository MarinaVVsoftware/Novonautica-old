<?php

namespace AppBundle\Form\Tienda;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolicitudType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fecha', DateType::class, array(
            'widget' => 'single_text',
            'html5' => false,
            'attr' => ['class' => 'datepicker-solo input-calendario',
                'readonly' => true],
            'format' => 'yyyy-MM-dd'
        ))
            ->add('nombrebarco',EntityType::class,[
                'class' => 'AppBundle:Barco',
                'label' => 'Embarcación',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador selectclientebuscar']
            ])

            ->add('solicitudEspecial', TextareaType::class, array(
                'attr' => array('rows' => 8),
            ))
            ->add('preciosolespecial', MoneyType::class, [
                'divisor' => 100,
                'label' => 'Precio de la Solicitud Especial',
                'currency' => 'MXN'
            ])
            ->add('producto', CollectionType::class, array(
            'entry_type' => PeticionType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
        ))
            ->add('subtotal' , MoneyType::class, [
                'divisor' => 100,
                'label' => false,
                'currency' => 'MXN',
                'attr' => ['readonly' => true]
            ])
            ->add('total', MoneyType::class, [
                'divisor' => 100,
                'label' => false,
                'currency' => 'MXN',
                'attr' => ['readonly' => true]
            ]);
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
        return 'appbundle_tienda_solicitud';
    }


}

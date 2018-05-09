<?php

namespace AppBundle\Form\Astillero;


use AppBundle\Form\Astillero\Proveedor\BancoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProveedorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('razonsocial',TextType::class,[
                'label' => 'Razón Social',
                'required' => false
            ])
            ->add('correo')
            ->add('telefono',TextType::class,[
                'label' => 'Teléfono'
            ])
            ->add('porcentaje',TextType::class,[
                'attr' => ['class'=>'esdecimal']
            ])
            ->add('tipo',ChoiceType::class,[
                'choices'=>[ 'Externo'=>'0','Interno'=>'1' ],
                'label'=>'Tipo'
            ])
            ->add('bancos',CollectionType::class,[
                'entry_type' => BancoType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('trabajos',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Proveedor\Trabajo',
                'label' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('rfc',TextType::class,[
                'label' => 'RFC',
                'required' => false
            ])
            ->add('direccionfiscal',TextType::class,[
                'label' => 'Dirección fiscal',
                'required' => false
            ])
            ->add('proveedorcontratista',ChoiceType::class,[
                'choices'=>[ 'Proveedor'=>'0','Contratista'=>'1' ],
                'label'=>'Tipo Trabajador'
            ])
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Proveedor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_proveedor';
    }


}

<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\TextType;
//use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class BarcoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'label' => 'Nombre de la embarcación'
            ])
            ->add('marca')
            ->add('modelo')
            ->add('anio',IntegerType::class,[
                'label' => 'Año'
            ])
            ->add('estatus',null,[
                'label' => ' '
            ])
//            ->add('cliente')
            ->add('motores',CollectionType::class,[
                'entry_type' => MotorType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Barco'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_barco';
    }


}

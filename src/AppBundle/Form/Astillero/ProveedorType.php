<?php

namespace AppBundle\Form\Astillero;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => 'Razón Social'
            ])
            ->add('porcentaje',TextType::class,[
                'attr' => ['class'=>'esdecimal']
            ])
            ->add('tipo',ChoiceType::class,[
                'choices'=>[ 'Externo'=>'0','Interno'=>'1' ],
                'label'=>'Tipo proveedor'
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

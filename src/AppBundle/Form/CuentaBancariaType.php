<?php

namespace AppBundle\Form;

use AppBundle\Entity\CuentaBancaria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuentaBancariaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('empresa',ChoiceType::class,[
                'choices' => array_flip(CuentaBancaria::getEmpresaLista()),
            ])
            ->add('moneda',ChoiceType::class,[
                'choices' => array_flip(CuentaBancaria::getMonedaLista())
            ])
            ->add('banco')
            ->add('sucursal')
            ->add('clabe',TextType::class,[
                'label' => 'CLABE Interbancaria'
            ])
            ->add('numCuenta',TextType::class,[
                'label' => 'Número de Cuenta'
            ])
            ->add('razonSocial',TextType::class,[
                'label' => 'Razón Social'
            ])
            ->add('rfc',TextType::class,[
                'label' => 'R.F.C.'
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CuentaBancaria'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cuentabancaria';
    }


}

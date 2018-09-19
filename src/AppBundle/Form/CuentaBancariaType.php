<?php

namespace AppBundle\Form;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\CuentaBancaria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuentaBancariaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'empresa',
            EntityType::class,
            [
                'class' => Emisor::class,
            ]
        );

        $builder->add(
            'moneda',
            ChoiceType::class,
            [
                'choices' => array_flip(CuentaBancaria::getMonedaLista()),
            ]
        );

        $builder->add('banco');
        $builder->add('sucursal');

        $builder->add(
            'clabe',
            TextType::class,
            [
                'label' => 'CLABE Interbancaria',
            ]
        );

        $builder->add(
            'numCuenta',
            TextType::class,
            [
                'label' => 'Número de Cuenta',
            ]
        );

        $builder->add(
            'razonSocial',
            TextType::class, [
                'label' => 'Razón Social',
            ]
        );
        $builder->add(
            'rfc',
            TextType::class, [
                'label' => 'R.F.C.',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => 'AppBundle\Entity\CuentaBancaria',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cuentabancaria';
    }


}

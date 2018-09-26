<?php

namespace AppBundle\Form\Cliente;

use AppBundle\Entity\Contabilidad\Banco;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuentaBancariaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias');

        $builder->add(
            'banco',
            EntityType::class,
            [
                'class' => Banco::class,
                'choice_label' => 'razonSocial'
            ]
        );

        $builder->add('numeroCuenta');
        $builder->add('clabe');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Cliente\CuentaBancaria',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente_cuentabancaria';
    }


}

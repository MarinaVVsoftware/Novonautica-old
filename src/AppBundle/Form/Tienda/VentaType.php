<?php

namespace AppBundle\Form\Tienda;

use AppBundle\Form\Tienda\Venta\ConceptoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VentaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moneySetting = [
            'label' => false,
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
        ];

        $builder->add(
            'descuento',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'iva',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'subtotal',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'total',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'conceptos',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Venta',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_venta';
    }


}

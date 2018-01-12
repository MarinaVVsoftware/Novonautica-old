<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad', NumberType::class)
            ->add('unidad', TextType::class)
            ->add('claveProdServ', TextType::class)
            ->add('claveUnidad', TextType::class)
            ->add('descripcion')
            ->add('valorunitario', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true
            ])
            ->add('descuento', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true
            ])
            ->add('iva', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true
            ])
            ->add('subtotal', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true
            ])
            ->add('total', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Facturacion\Concepto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion_concepto';
    }


}

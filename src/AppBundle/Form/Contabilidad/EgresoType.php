<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Entity\Contabilidad\Egreso\Tipo;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Form\Contabilidad\Egreso\EntradaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EgresoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'fecha',
            DateType::class,
            [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker-solo input-calendario',
                    'readonly' => true,
                ],
                'format' => 'yyyy-MM-dd',
            ]
        );

        $builder->add(
            'empresa',
            EntityType::class,
            [
                'class' => Emisor::class,
                'required' => true,
            ]
        );

        $builder->add(
            'tipo',
            EntityType::class,
            [
                'class' => Tipo::class,
                'choice_label' => 'descripcion',
                'required' => true,
            ]
        );

        $builder->add(
            'entradas',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => EntradaType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );

        $builder->add(
            'total',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'data' => 0,
                'attr' => ['class' => 'money-input'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Egreso',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_egreso';
    }


}

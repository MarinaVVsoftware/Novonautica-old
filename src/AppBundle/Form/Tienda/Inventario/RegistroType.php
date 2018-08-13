<?php

namespace AppBundle\Form\Tienda\Inventario;

use AppBundle\Form\Tienda\Inventario\Registro\EntradaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistroType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('referencia');
        $builder->add(
            'fecha',
            DateTimeType::class,
            [
                'data' => new \DateTime(),
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
                'empty_data' => 0,
                'attr' => ['class' => 'total-type'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Inventario\Registro',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_inventario_registro';
    }


}

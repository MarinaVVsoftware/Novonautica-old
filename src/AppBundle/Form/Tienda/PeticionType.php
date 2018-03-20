<?php

namespace AppBundle\Form\Tienda;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeticionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('peticion', EntityType::class, [
            'label' => 'Producto',
            'choice_attr' => function($val, $key, $index)
            {
              return ['data-precio' => $val->getPrecio()];
            },
            'class' => 'AppBundle:Tienda\Producto',
            'placeholder' => 'Seleccionar...',
            'attr' => ['class' => 'select-buscador selectclientebuscar']
            ])
        ->add('cantidad', IntegerType::class, [
            'data' => 1,
            'empty_data' => 1,
            'attr' => ['min' => 1, 'class' => 'cantidad']
        ])
        ->add('cantidadEntregado', HiddenType::class, [
            'data' => 1,
            'empty_data' => 1,
            'attr' => ['class' => 'entregado']
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Peticion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_peticion';
    }
}
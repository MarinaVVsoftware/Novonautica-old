<?php

namespace AppBundle\Form\Astillero;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('precio',MoneyType::class,[
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
                'label' => 'Precio (MXN)'
            ])
            ->add('unidad')
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'attr' => ['rows'=>5]
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Servicio'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_servicio';
    }


}

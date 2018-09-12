<?php

namespace AppBundle\Form\Astillero;

use AppBundle\Entity\Astillero\Producto;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
                'label' => 'Precio'
            ])
            ->add('divisa',ChoiceType::class,[
                'choices' => ['USD' => 'USD', 'MXN' => 'MXN'],
            ])
            ->add('unidad')
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'attr' => ['rows'=>5],
                'required' => false
            ])
//            ->add('productos',EntityType::class,[
//                'class' => 'AppBundle\Entity\Astillero\Producto' ,
//                'choice_label' => 'nombre',
//                'multiple' => true,
//                'expanded' => true,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('p')
//                        ->orderBy('p.nombre', 'ASC')
//                        ;
//                },
//                'label' => false
//            ])
            ->add('gruposProductos',CollectionType::class,[
                'entry_type' => GrupoProductoType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('tipoCantidad', ChoiceType::class, [
                'label' => 'Tipo de cantidad',
                'choices' => ['En base al eslora' => true, 'Fijo' => false],
            ])
            ->add('diasDescuento');
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

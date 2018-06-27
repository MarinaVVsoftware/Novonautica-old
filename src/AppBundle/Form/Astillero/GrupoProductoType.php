<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 02/07/2018
 * Time: 03:17 PM
 */

namespace AppBundle\Form\Astillero;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('producto',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Producto',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class'=>'select-buscador'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.nombre', 'ASC');
                },
            ])
            ->add('tipoCantidad',ChoiceType::class,[
                'label' => 'Tipo de cantidad',
                'choices' => ['Cantidad Fija' => 0, 'Promedio por pie' => 1]
            ])
            ->add('cantidad',TextType::class,[
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('observaciones');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\GrupoProducto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_grupo_producto';
    }
}
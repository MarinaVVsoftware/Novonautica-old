<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 31/10/2018
 * Time: 03:29 PM
 */

namespace AppBundle\Form\Solicitud;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('cantidad',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
            ])
            ->add('marinaServicio',EntityType::class,[
                'class' => 'AppBundle\Entity\MarinaHumedaServicio',
                'placeholder' => 'Seleccionar...',
                'required' => false,
                'choice_label' => function ($concepto) {
                    return $concepto.' - '.$concepto->getClaveUnidad();
                }
            ])
            ->add('combustibleCatalogo',EntityType::class,[
                'class' => 'AppBundle\Entity\Combustible\Catalogo',
                'placeholder' => 'Seleccionar...',
                'required' => false,
            ])
            ->add('astilleroProducto',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Producto',
                'placeholder' => 'Seleccionar...',
                'required' => false,
                'choice_label' => function ($concepto) {
                    return $concepto.' - '.$concepto->getClaveUnidad();
                }
            ])
            ->add('tiendaProducto',EntityType::class,[
                'class' => 'AppBundle\Entity\Tienda\Producto',
                'placeholder' => 'Seleccionar...',
                'required' => false,
                'choice_label' => function ($concepto) {
                    return $concepto.' - '.$concepto->getClaveUnidad();
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Solicitud\Concepto',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_solicitud_concepto';
    }
}
<?php

namespace AppBundle\Form\Compra;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ConceptoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formatoMoney = [
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
            'attr' => ['class' => 'esdecimal', 'readonly' => 'readonly'],
        ];

        $builder
            ->add('cantidad',TextType::class,[
                'attr' => ['class' => 'esdecimal', 'readonly' => 'readonly'],
            ])
            ->add('marinaServicio',EntityType::class,[
                'class' => 'AppBundle\Entity\MarinaHumedaServicio',
                'placeholder' => 'Seleccionar...',
                'required' => false,
                'choice_label' => function ($concepto) {
                    return $concepto.' - '.$concepto->getClaveUnidad();
                },
            ])
            ->add('combustibleCatalogo',EntityType::class,[
                'class' => 'AppBundle\Entity\Combustible\Catalogo',
                'placeholder' => 'Seleccionar...',
                'required' => false
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
            ])
            ->add('proveedor',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Proveedor',
                'placeholder' => 'Selecionar...',
                'constraints' => [new NotNull(['message' => 'Por favor selecciona un proveedor'])],
                'required' => false,
            ])
            ->add('precio',MoneyType::class,[
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal'],
            ])
            ->add('subtotal',MoneyType::class,$formatoMoney)
            ->add('ivatotal',MoneyType::class,$formatoMoney)
            ->add('total',MoneyType::class,$formatoMoney);
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
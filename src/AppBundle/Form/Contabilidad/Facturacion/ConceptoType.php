<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Form\DataTransformer\ClaveProdServTransformer;
use AppBundle\Form\DataTransformer\ClaveUnidadTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptoType extends AbstractType
{
    private $cpsTransformer;
    private $cuTransformer;

    public function __construct(ClaveProdServTransformer $cpsTransformer, ClaveUnidadTransformer $cuTransformer)
    {
        $this->cpsTransformer = $cpsTransformer;
        $this->cuTransformer = $cuTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad', NumberType::class)
            ->add('claveProdServAC', TextType::class, ['mapped' => false])
            ->add('claveProdServ', HiddenType::class)
            ->add('claveUnidadAC', TextType::class, ['mapped' => false])
            ->add('claveUnidad', HiddenType::class)
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


        $builder->get('claveProdServ')
            ->addModelTransformer($this->cpsTransformer);
        $builder->get('claveUnidad')
            ->addModelTransformer($this->cuTransformer);
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

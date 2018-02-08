<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2/8/18
 * Time: 14:46
 */

namespace AppBundle\Form\Contabilidad\Facturacion;


use AppBundle\Entity\Pago;
use AppBundle\Form\DataTransformer\FacturaPagosDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturaPagoType extends AbstractType
{
    private $transformer;

    public function __construct(FacturaPagosDataTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer, true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'No se encontraron los pagos elegidos',
            'class' => 'AppBundle:Pago',
            'multiple' => true,
            'choice_label' => function ($pago) {
                return '$' . number_format(($pago->getCantidad() / 100), 2);
            }
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
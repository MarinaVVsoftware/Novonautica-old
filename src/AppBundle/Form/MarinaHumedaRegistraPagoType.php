<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 27/11/2017
 * Time: 04:25 PM
 */

namespace AppBundle\Form;


use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\Pago;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MarinaHumedaRegistraPagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MarinaHumedaCotizacion $cotizacionMarina */
        $cotizacionMarina = $options['data'];

        // Los childs de las colecciones no reciben datos a traves de valores por defecto a traves de parents
        // u otras opciones comunes en formtype
        // Para poder asignar valores por defecto se tiene que iniciar el objeto y ese objeto servira como los valores
        // del prototipo, ya que estos solo sirven como referencia en la vista.
        $pagoViewDefaultData = new Pago();
        $pagoViewDefaultData->setDolar($cotizacionMarina->getDolar());
        $pagoViewDefaultData->setCantidad($cotizacionMarina->getTotal() - $cotizacionMarina->getPagado());

        $builder
            ->add('pagos', CollectionType::class, [
                'entry_type' => FullDataPagoType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype_data' => $pagoViewDefaultData,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MarinaHumedaCotizacion::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizacion';
    }
}

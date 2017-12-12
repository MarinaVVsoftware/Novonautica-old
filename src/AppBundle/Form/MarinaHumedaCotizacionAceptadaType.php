<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 22/11/2017
 * Time: 04:32 PM
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MarinaHumedaCotizacionAceptadaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('metodopago',ChoiceType::class,[
                    'choices'  => [
                        'Efectivo' => 'Efectivo',
                        'Tarjeta de crédito' => 'Tarjeta de crédito',
                        'Transferencia' => 'Transferencia',
                    ],
                'label' => 'Método de pago'
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizacion';
    }
}
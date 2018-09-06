<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moneyOptions = [
            'currency' => false,
            'divisor' => 100,
            'grouping' => true,
        ];

        $builder->add('numeroParcialidad');

        $builder->add(
            'importeSaldoAnterior',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'importePagado',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'importeSaldoInsoluto',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'formaPagoPagos',
            ChoiceType::class,
            [
                'choices' => Facturacion::$formasPagos,
            ]
        );

        $builder->add(
            'monedaPagos',
            ChoiceType::class,
            [
                'choices' => Facturacion::$monedas,
            ]
        );

        $builder->add(
            'tipoCambioPagos',
            MoneyType::class,
            [
                'label' => 'Tipo de cambio',
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
            ]
        );

        $builder->add(
            'montoPagos',
            MoneyType::class,
            $moneyOptions
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $pago = $event->getData();
                $form = $event->getForm();

                $form->add(
                    'uuidFacturaRelacionada',
                    TextType::class,
                    [
                        'label' => 'UUID',
                        'disabled' => null !== $pago->getFactura(),
                    ]
                );

                $form->add(
                    'monedaFacturaRelacionada',
                    ChoiceType::class,
                    [
                        'label' => 'Moneda',
                        'choices' => Facturacion::$monedas,
                        'disabled' => null !== $pago->getFactura(),
                    ]
                );

                $form->add(
                    'metodoPagoFacturaRelacionada',
                    ChoiceType::class,
                    [
                        'label' => 'Metodo de pago',
                        'choices' => Facturacion::$metodosPagos,
                        'disabled' => null !== $pago->getFactura(),
                    ]
                );
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facturacion\Pago::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion_pago';
    }


}

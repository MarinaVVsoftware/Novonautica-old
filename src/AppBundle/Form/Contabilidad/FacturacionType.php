<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\Pago;
use AppBundle\Form\Contabilidad\Facturacion\ConceptoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturacionType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moneySetting = [
            'label' => false,
            'currency' => 'USD',
            'divisor' => 100,
            'required' => false,
            'grouping' => true
        ];

        $builder
            ->add('emisor', EntityType::class, [
                'class' => 'AppBundle\Entity\Contabilidad\Facturacion\Emisor'
            ])
            ->add('rfc', TextType::class, ['label' => 'RFC'])
            ->add('cliente')
            ->add('razonSocial')
            ->add('direccionFiscal')
            ->add('numeroTelefonico')
            ->add('email', TextType::class, ['label' => 'Emails de recepción (Separados por comas)'])
            ->add('conceptos', CollectionType::class, [
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
            ->add('formaPago', ChoiceType::class, [
                'label' => 'Método de pago',
                'choices' => [
                    'Efectivo' => '01',
                    'Cheque nominativo' => '02',
                    'Transferencia electrónica de fondos' => '03',
                    'Tarjeta de crédito' => '04',
                    'Monedero electrónico' => '05',
                    'Dinero electrónico' => '06',
                    'Vales de despensa' => '08',
                    'Dación en pago' => '12',
                    'Pago por subrogación' => '13',
                    'Pago por consignación' => '14',
                    'Condonación' => '15',
                    'Compensación' => '17',
                    'Novación' => '23',
                    'Confusión' => '24',
                    'Remisión de deuda' => '25',
                    'Prescripción o caducidad' => '26',
                    'A satisfacción del acreedor' => '27',
                    'Tarjeta de débito' => '28',
                    'Tarjeta de servicios' => '29',
                    'Por definir' => '99',
                ]
            ])
            ->add('metodoPago', ChoiceType::class, [
                'label' => 'Método de pago',
                'choices' => [
                    'Pago en una sola exhibición' => 'PUE',
                    'Pago inicial y parcialidades' => 'PIP',
                    'Pago en parcialidades o diferido' => 'PPD',
                ]
            ])
            ->add('tipoComprobante', ChoiceType::class, [
                'label' => 'Tipo de comprobante',
                'choices' => [
                    'Ingreso' => 'I',
                    'Egreso' => 'E',
                    'Traslado' => 'T',
                    'Nómina' => 'N',
                    'Pago' => 'P',
                ]
            ])
            ->add('usoCFDI', ChoiceType::class, [
                'label' => 'Uso CFDI',
                'choices' => [
                    'Adquisición de mercancias' => 'G01',
                    'Devoluciones, descuentos o bonificaciones' => 'G02',
                    'Gastos en general' => 'G03',
                    'Construcciones' => 'I01',
                    'Mobilario y equipo de oficina por inversiones' => 'I02',
                    'Equipo de transporte' => 'I03',
                    'Equipo de computo y accesorios' => 'I04',
                    'Dados, troqueles, moldes, matrices y herramental' => 'I05',
                    'Comunicaciones telefónicas' => 'I06',
                    'Comunicaciones satelitales' => 'I07',
                    'Otra maquinaria y equipo' => 'I08',
                    'Honorarios médicos, dentales y gastos hospitalarios.' => 'D01',
                    'Gastos médicos por incapacidad o discapacidad' => 'D02',
                    'Gastos funerales.' => 'D03',
                    'Donativos.' => 'D04',
                    'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).' => 'D05',
                    'Aportaciones voluntarias al SAR.' => 'D06',
                    'Primas por seguros de gastos médicos.' => 'D07',
                    'Gastos de transportación escolar obligatoria.' => 'D08',
                    'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.' => 'D09',
                    'Pagos por servicios educativos (colegiaturas)' => 'D10',
                    'Por definir' => 'P01',
                ]
            ])
            ->add('tipoCambio', MoneyType::class, [
                'label' => 'Tipo de cambio',
                'currency' => 'USD',
                'divisor' => 100,
                'required' => false,
                'grouping' => true
            ])
            ->add('descuento', MoneyType::class, $moneySetting)
            ->add('subtotal', MoneyType::class, $moneySetting)
            ->add('iva', MoneyType::class, $moneySetting)
            ->add('total', MoneyType::class, $moneySetting)
            ->add('moneda', ChoiceType::class, [
                'label' => 'Moneda a facturar',
                'choices' => [
                    'USD' => 'USD',
                    'MXN' => 'MXN'
                ]
            ])
            ->add('folioCotizacion', TextType::class, [
                'label' => 'Folio de cotización',
                'required' => false
            ]);

        $formBuilder = function (FormInterface $form, $folioCotizacion = null) {
            $folios = explode('-', $folioCotizacion);
            $facturacionRepo = $this->em->getRepository('AppBundle:Contabilidad\Facturacion');
            $pagos = $folioCotizacion ? $facturacionRepo->getPagosByFolioCotizacion($folios[0], $folios[1] ?? null) : [];

            $form->add('pagos', EntityType::class, [
                'class' => 'AppBundle\Entity\Pago',
                'required' => false,
                'placeholder' => '',
                'choices' => $pagos,
                'choice_label' => function ($pago) {
                    /** @var Pago $pago */
                    return '$' . number_format(($pago->getCantidad() / 100), 2);
                }
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formBuilder) {
                $form = $event->getForm();
                $formBuilder($form, $event->getData()->getFolioCotizacion());
            });

        $builder->get('folioCotizacion')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formBuilder) {
                $form = $event->getForm()->getParent();
                $formBuilder($form, $event->getForm()->getData());
            }
        );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => 'AppBundle\Entity\Contabilidad\Facturacion']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion';
    }


}

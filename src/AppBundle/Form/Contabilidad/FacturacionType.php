<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Form\Contabilidad\Facturacion\ConceptoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturacionType extends AbstractType
{
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
            ->add('email')
            ->add('folioCotizacion', TextType::class, [
                'label' => 'Folio de cotización',
                'required' => false
            ])
            ->add('cotizacion', HiddenType::class)
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
            ->add('total', MoneyType::class, $moneySetting);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Facturacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion';
    }


}

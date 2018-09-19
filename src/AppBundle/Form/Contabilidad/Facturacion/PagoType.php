<?php

namespace AppBundle\Form\Contabilidad\Facturacion;

use AppBundle\Entity\Cliente\CuentaBancaria;
use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class PagoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

        $builder->add(
            'folio',
            HiddenType::class
        );

        $builder->add('numeroParcialidad');

        $builder->add(
            'fechaPagos',
            DateTimeType::class,
            [
                'label' => 'Fecha de pago',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'readonly' => true,
                ],
                'format' => 'dd-MM-yyyy',
            ]
        );

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
            [
                'label' => 'Monto',
                'currency' => false,
                'divisor' => 100,
                'grouping' => true,
            ]
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

                $this->createCuentaOrdenanteField($form);
            });

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $cuenta = array_key_exists('cuentaOrdenante', $data)
                    ? $this->entityManager->getRepository(CuentaBancaria::class)->find($data['cuentaOrdenante'])
                    : null;

                $this->createCuentaOrdenanteField($form, $cuenta);
            }
        );
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

    private function createCuentaOrdenanteField(FormInterface $form, CuentaBancaria $cuenta = null)
    {
        $cuentas = null === $cuenta ? [] : [$cuenta];

        $form->add(
            'cuentaOrdenante',
            EntityType::class,
            [
                'class' => CuentaBancaria::class,
                'choice_label' => 'alias',
                'choices' => $cuentas,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona una cuenta de cliente']),
                    new NotBlank(['message' => 'Por favor selecciona una cuenta de cliente']),
                ],
            ]
        );
    }
}

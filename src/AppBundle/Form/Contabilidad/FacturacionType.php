<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Cliente\RazonSocial;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Form\Contabilidad\Facturacion\ConceptoType;
use AppBundle\Validator\Constraints\FacturaPuedeTimbrar;
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
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotNull;

class FacturacionType extends AbstractType
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
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
        ];

        $builder->add(
            'emisor',
            EntityType::class,
            [
                'class' => Facturacion\Emisor::class,
                'choice_label' => 'alias',
            ]
        );

        $builder->add(
            'condicionesPago',
            TextType::class,
            [
                'label' => 'Condiciones de pago',
            ]
        );

        $builder->add(
            'formaPago',
            ChoiceType::class,
            [
                'label' => 'Forma de pago',
                'choices' => Facturacion::$formasPagos,
            ]
        );

        $builder->add(
            'lugarExpedicion',
            TextType::class,
            [
                'label' => 'Lugar de expedición',
            ]
        );

        $builder->add(
            'metodoPago',
            ChoiceType::class,
            [
                'label' => 'Método de pago',
                'choices' => Facturacion::$metodosPagos,
            ]
        );

        $builder->add(
            'tipoComprobante',
            ChoiceType::class,
            [
                'label' => 'Tipo de comprobante',
                'choices' => Facturacion::$tiposComprobantes,
            ]
        );

        $builder->add(
            'subtotal',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'total',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'impuesto',
            ChoiceType::class,
            [
                'choices' => Facturacion::$impuestos,
            ]
        );

        $builder->add(
            'tasa',
            TextType::class,
            [
                'label' => 'Valor de factor'
            ]
        );

        $builder->add(
            'importe',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'tipoFactor',
            ChoiceType::class,
            [
                'label' => 'Tipo de factor',
                'choices' => Facturacion::$factores,
            ]
        );

        $builder->add(
            'totalImpuestosTransladados',
            MoneyType::class,
            $moneyOptions
        );

        $builder->add(
            'conceptos',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'prototype_data' => new Facturacion\Concepto(),
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Debes agregar al menos un concepto',
                    ])
                ],
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->createClienteField($event->getForm());
                $this->createReceptorField($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $cliente = array_key_exists('cliente', $data)
                    ? $this->entityManager->getRepository(Cliente::class)->find($data['cliente'])
                    : null;

                $this->createClienteField($event->getForm(), $cliente);
                $this->createReceptorField($event->getForm(), $cliente);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contabilidad\Facturacion',
            'constraints' => [
                new FacturaPuedeTimbrar()
            ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contabilidad_facturacion';
    }

    private function createClienteField(FormInterface $form, Cliente $cliente = null)
    {
        $clientes = null === $cliente ? [] : [$cliente];

        $form->add(
            'cliente',
            EntityType::class,
            [
                'class' => Cliente::class,
                'choice_label' => 'nombre',
                'choices' => $clientes,
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un cliente']),
                ],
            ]
        );
    }

    private function createReceptorField(FormInterface $form, Cliente $cliente = null)
    {
        $rfcs = null === $cliente
            ? []
            : $this->entityManager->getRepository(RazonSocial::class)->findBy(['cliente' => $cliente]);

        $form->add(
            'receptor',
            EntityType::class,
            [
                'class' => RazonSocial::class,
                'choice_label' => 'razonSocial',
                'choices' => $rfcs,
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un RFC receptor']),
                ],
            ]
        );
    }
}

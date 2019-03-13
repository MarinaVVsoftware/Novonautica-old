<?php

namespace AppBundle\Form\Contabilidad;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Cliente\RazonSocial;
use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Extra\FacturacionHelper;
use AppBundle\Form\Contabilidad\Facturacion\ConceptoType;
use AppBundle\Validator\Constraints\FacturaEstaTimbrada;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class FacturacionType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
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
            'constraints' => new NotBlank(['message' => 'Este campo no puede estar vacio']
            )
        ];

        $builder->add(
            'folio',
            HiddenType::class
        );

        $builder->add(
            'emisor',
            EntityType::class,
            [
                'class' => Facturacion\Emisor::class,
                'choice_label' => 'alias',
                'placeholder' => 'Seleccione un emisor',
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('e');
                    $views = [];

                    foreach ($this->security->getUser()->getRoles() as $role) {
                        if (strpos($role, 'ROLE_ADMIN') === 0) {
                            return $query;
                        }

                        if (strpos($role, 'VIEW_EGRESO') === 0) {
                            $views[] = explode('_', $role)[3];
                        }
                    }

                    return $query->where(
                        $query->expr()->in('e.id', $views)
                    );
                },
                'constraints' => [
                    new NotNull(
                        ['message' => 'Por favor selecciona un emisor']
                    ),
                ],
            ]
        );

        $builder->add(
            'condicionesPago',
            TextType::class,
            [
                'label' => 'Condiciones de pago',
                'required' => false,
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
                'constraints' => new NotBlank(['message' => 'Este campo no puede estar vacio'])
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
            'moneda',
            ChoiceType::class,
            [
                'choices' => Facturacion::$monedas,
            ]
        );

        $builder->add(
            'tipoCambio',
            MoneyType::class,
            $moneyOptions
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
                'label' => 'Valor de factor',
                'constraints' => new NotBlank(['message' => 'Este campo no puede estar vacio'])
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
                    new Count(
                        [
                            'min' => 1,
                            'minMessage' => 'Debes agregar al menos un concepto',
                        ]
                    ),
                ],
            ]
        );

        /*
         * Unmapped fields
         */
        $builder->add(
            'fechaFiltro',
            DateType::class,
            [
                'mapped' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'MMMM yyyy',
                'attr' => ['autocomplete' => 'off'],
            ]
        );

        /*
         * Event listeners
         */

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->createClienteField($event->getForm());
                $this->createReceptorField($event->getForm());
                $this->createCotizacionesField($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $fecha = \DateTime::createFromFormat('F Y', $data['fechaFiltro']);

                $cliente = array_key_exists('cliente', $data)
                    ? $this->entityManager->getRepository(Cliente::class)->find($data['cliente'])
                    : null;

                $cotizaciones = array_key_exists('cotizaciones', $data)
                    ? FacturacionHelper::getCotizaciones($this->entityManager, $data['emisor'], $data['cliente'],
                        $fecha->format('Y-m-d'))
                    : [];

                $cotizacionesChoices = [];
                foreach ($cotizaciones as $cotizacion) {
                    $cotizacionesChoices[$cotizacion['text']] = $cotizacion['id'];
                }

                $this->createClienteField($event->getForm(), $cliente);
                $this->createReceptorField($event->getForm(), $cliente);
                $this->createCotizacionesField($event->getForm(), $cotizacionesChoices);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facturacion::class,
            'constraints' => [
                new FacturaEstaTimbrada(['groups' => 'Timbrado']),
            ],
        ]);
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
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotBlank(['message' => 'Por favor selecciona un cliente']),
                ],
            ]
        );
    }

    private function createReceptorField(FormInterface $form, Cliente $cliente = null)
    {
        $receptorRepository = $this->entityManager->getRepository(RazonSocial::class);

        $rfcs = null === $cliente
            ? []
            : $receptorRepository->findBy(['cliente' => $cliente]);

        if ($rfcs) {
            $rfcs[] = $receptorRepository->find(84);
        }

        $form->add(
            'receptor',
            EntityType::class,
            [
                'class' => RazonSocial::class,
                'choice_label' => 'razonSocial',
                'choices' => $rfcs,
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotBlank(['message' => 'Por favor selecciona un cliente']),
                ],
            ]
        );
    }

    private function createCotizacionesField(FormInterface $form, $choices = null)
    {
        $form->add(
            'cotizaciones',
            ChoiceType::class,
            [
                'choices' => $choices,
                'placeholder' => 'Seleccione una cotización',
                'constraints' => [
                    new NotBlank(['message' => 'Por favor selecciona una cotizacion']),
                ],
            ]
        );
    }
}

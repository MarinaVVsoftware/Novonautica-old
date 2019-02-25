<?php

namespace AppBundle\Form;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\Pincode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AstilleroCotizacionType extends AbstractType
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
        $builder
            ->add('barco', EntityType::class, [
                'class' => 'AppBundle:Barco',
                'label' => 'Barco',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador selectbarcobuscar'],
            ])
            ->add('dolar', MoneyType::class, [
                'required' => false,
                'attr' => ['class' => 'esdecimal', 'autocomplete' => 'off'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('descuento', NumberType::class, [
                'label' => 'Descuento %',
                'empty_data' => 0,
                'attr' => [
                    'class' => 'esdecimal limite100',
                    'autocomplete' => 'off',
                    'max' => 100,
                    'min' => 0,
                ],
            ])
            ->add('mensaje', TextareaType::class, [
                'label' => 'Mensaje en el correo:',
                'attr' => ['rows' => 7, 'class' => 'editorwy'],
                'required' => false,
            ])
            ->add('acservicios', CollectionType::class, [
                'entry_type' => AstilleroCotizaServicioType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'attr' => ['prototype2' => ' '],
                'by_reference' => false,
            ])
            ->add('notificarCliente', CheckboxType::class, [
                'label' => '¿Notificar al cliente?',
                'required' => false,
            ])
            ->add('requerirFactura', CheckboxType::class, [
                'label' => '¿Requiere factura?',
                'required' => false,
            ])
            ->add('guardareditable', SubmitType::class, [
                'label' => 'Guardar y editar después',
                'attr' => ['class' => 'btn btn-azul inline-block'],
            ])
            ->add('guardarfinalizar', SubmitType::class, [
                'label' => 'Guardar y finalizar',
                'attr' => ['class' => 'btn btn-naranja inline-block'],
            ]);

        $builder->add(
            'pincode',
            TextType::class,
            [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'minlength' => 8,
                    'maxlength' => 8,
                ],
                'constraints' => [
                    new Assert\Callback([$this, 'validatePincode']),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Un Pincode es de exactamente 8 digitos',
                    ])
                ],
            ]
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();
            if ($cotizacion->getFechaLlegada() == null) {
                $form
                    ->add('fechaLlegada', DateType::class, [
                        'label' => 'Fecha inicio',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime(),
                    ])
                    ->add('fechaSalida', DateType::class, [
                        'label' => 'Fecha fin',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime('+1 week'),
                    ])
                    ->add('diasEstadia', TextType::class, [
                        'label' => 'Días Estadia',
                        'attr' => ['class' => 'esnumero'],
                        'data' => '6',
                    ]);

            } else {
                $form
                    ->add('fechaLlegada', DateType::class, [
                        'label' => 'Fecha inicio',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ])
                    ->add('fechaSalida', DateType::class, [
                        'label' => 'Fecha fin',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ])
                    ->add('diasEstadia', TextType::class, [
                        'label' => 'Días Estadia',
                        'attr' => ['class' => 'esnumero'],
                    ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AstilleroCotizacion::class,
        ]);
    }

    public function validatePincode($firma, ExecutionContextInterface $context)
    {
        /** @var Form $form */
        $form = $context->getRoot();
        $cotizacion = $form->getData();

        if (!$cotizacion instanceof AstilleroCotizacion) {
            return;
        }

        if (
            'guardarfinalizar' === $form->getClickedButton()->getName()
            && $cotizacion->getDescuento() > 0
        ) {
            $pincodeRepository = $this->entityManager->getRepository(Pincode::class);
            $pincode = $pincodeRepository->getOneValid($firma);

            if (!$pincode) {
                $context->buildViolation('El Pincode utilizado no existe o ha expirado')
                    ->atPath('pincode')
                    ->addViolation();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocotizacion';
    }
}

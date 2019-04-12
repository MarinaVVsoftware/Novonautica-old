<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Cliente;
use Symfony\Component\Validator\Constraints as Assert;

class MarinaHumedaCotizacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cliente', EntityType::class, [
                'class' => 'AppBundle:Cliente',
                'label' => 'Cliente',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador selectclientebuscar'],

            ])
            ->add('fechaLlegada', DateType::class, [
                'label' => 'Fecha llegada',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
            ])
            ->add('fechaSalida', DateType::class, [
                'label' => 'Fecha Salida',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime('+1 week')
            ])
            ->add('diasEstadia',TextType::class,[
                'label'=>'Días Estadia',
                'attr' => ['class' => 'esnumero','readonly' => true],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Debe seleccionar rango de fechas'
                    ])
                ]
            ])
            ->add('descuentoEstadia', NumberType::class, [
                'label' => 'Descuento estadía %',
                'attr' => [
                    'class' => 'esdecimal limite100',
                    'autocomplete' => 'off',
                    'max' => 100,
                    'min' => 0,
                    'readonly' => true
                    ],
                'required' => false
            ])
            ->add('descuentoElectricidad', NumberType::class, [
                'label' => 'Descuento electricidad %',

                'attr' => ['class' => 'esdecimal limite100',
                    'autocomplete' => 'off',
                    'max' => 100,
                    'min' => 0,
                    'readonly' => true
                ],
                'required' => false,
            ])
            ->add('dolar', MoneyType::class, [
                'required'=>false,
                'attr' => [
                    'class' => 'esdecimal',
                    'autocomplete' => 'off',
                    'readonly' => true
                ],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('mensaje', TextareaType::class, [
                'label' => 'Mensaje en el correo:',
                'attr' => ['rows' => 7, 'class' => 'editorwy'],
                'required' => false,
            ])
            ->add('mhcservicios', CollectionType::class, [
                'entry_type' => MarinaHumedaCotizaServiciosType::class,
                'label' => false
            ])
            ->add('validanovo', ChoiceType::class, [
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function ($val, $key, $index) {
                    return ['class' => 'opcion' . strtolower($key)];
                },
            ])
            ->add('notasnovo', TextareaType::class, [
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
            ->add('validacliente', ChoiceType::class, [
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function ($val, $key, $index) {
                    return ['class' => 'opcion' . strtolower($key)];
                },
            ])
            ->add('notascliente', TextareaType::class, [
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
            ->add('notificarCliente', CheckboxType::class, [
                'label' => '¿Notificar al cliente?',
                'required' => false
            ])
            ->add('estatusPincode',HiddenType::class,[
                'data' => '0',
                'mapped' => false
            ]);

        $formModifier = function (FormInterface $form, Cliente $cliente = null) {
            $barcos = null === $cliente ? array() : $cliente->getBarcos();

            $form->add('barco', EntityType::class, array(
                'class' => 'AppBundle:Barco',
                'placeholder' => '',
                'attr' => ['class' => 'busquedabarco'],
                'choices' => $barcos,
                'expanded' => true,
                'multiple' => false
            ));

        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $cotizacion = $event->getData();
            $form = $event->getForm();
            if ($cotizacion->getId() == null) { //cotización nueva
                $form
                    ->remove('validanovo')
                    ->remove('validacliente')
                    ->remove('notasnovo')
                    ->remove('notascliente');

                if($cotizacion->getFechaLlegada()){
                    $form->add('fechaLlegada', DateType::class, [
                        'label' => 'Fecha llegada',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ]);
                }
                if($cotizacion->getFechaSalida()){
                    $form->add('fechaSalida', DateType::class, [
                        'label' => 'Fecha Salida',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ]);
                }

                $formModifier($event->getForm(), $cotizacion->getCliente());
            }
            // para validar por el cliente
            else if($cotizacion->getValidanovo() == 2) {
                $form
                    ->remove('cliente')
                    ->remove('fechaLlegada')
                    ->remove('fechaSalida')
                    ->remove('diasEstadia')
                    ->remove('descuentoEstadia')
                    ->remove('descuentoElectricidad')
                    ->remove('dolar')
                    ->remove('mensaje')
                    ->remove('mhcservicios')
                    ->remove('validanovo')
                    ->remove('notasnovo')
                    ->remove('slip')
                    ->remove('notificarCliente');
            }
            //para validar por novo
            else {
                $form
                    ->remove('cliente')
                    ->remove('fechaLlegada')
                    ->remove('fechaSalida')
                    ->remove('diasEstadia')
                    ->remove('descuentoEstadia')
                    ->remove('descuentoElectricidad')
                    ->remove('dolar')
                    ->remove('mensaje')
                    ->remove('mhcservicios')
                    ->remove('validacliente')
                    ->remove('notascliente')
                    ->remove('notificarCliente')
                    ->remove('slip');
            }
        });

        $builder->get('cliente')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $cliente = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $cliente);
            }
        );
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

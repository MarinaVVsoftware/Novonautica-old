<?php

namespace AppBundle\Form;


use AppBundle\Entity\MarinaHumedaCotizaServicios;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Cliente;

class MarinaHumedaCotizacionTodoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cliente',EntityType::class,[
                'class' => 'AppBundle:Cliente',
                'label' => 'Cliente',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador'],

            ])
            ->add('fechaLlegada',DateType::class,[
                'label' => 'Fecha llegada',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('fechaSalida',DateType::class,[
                'label' => 'Fecha Salida',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('descuento',null,[
                'empty_data' => 0,
                'attr' => ['class' => 'esdecimal']
            ])

            ->add('mhcservicios',CollectionType::class,[
                'entry_type' => MarinaHumedaCotizaServiciosType::class,
                'label' => false
            ])
            ->add('validanovo', ChoiceType::class,[
                'choices' =>[ 'Aceptar' => 2, 'Rechazar' => 1, 'Pendiente' => 0 ],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function($val, $key, $index) {
                    return ['class' => 'opcion'.strtolower($key)];
                },
            ])
            ->add('notasnovo',TextareaType::class,[
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
            ->add('validacliente', ChoiceType::class,[
                'choices' =>[ 'Aceptar' => 2, 'Rechazar' => 1, 'Pendiente' => 0 ],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function($val, $key, $index) {
                    return ['class' => 'opcion'.strtolower($key)];
                },
            ])
            ->add('notascliente',TextareaType::class,[
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
        ;

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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getCliente());
            }
        );

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
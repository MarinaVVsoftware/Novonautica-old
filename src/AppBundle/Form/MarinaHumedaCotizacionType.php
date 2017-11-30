<?php

namespace AppBundle\Form;


//use Doctrine\DBAL\Types\FloatType;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\Slip;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\ValorSistema;

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
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('fechaSalida', DateType::class, [
                'label' => 'Fecha Salida',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('descuento', null, [
                'empty_data' => 0,
                'attr' => ['class' => 'esdecimal',
                    'autocomplete' => 'off']
            ])
            ->add('dolar', null, [
                'empty_data' => 0,
                'attr' => ['class' => 'esdecimal',
                    'autocomplete' => 'off']
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
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1, 'Pendiente' => 0],
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
            ->add('slip', EntityType::class, [
                'class' => 'AppBundle:Slip',
                'label' => 'Slip',
                'placeholder' => 'Seleccionar...',
                'choice_attr' => function ($slip) {
                    /** @var Slip $slip */
                    return ['data-feet' => $slip->getPies()];
                }
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
                $formModifier($event->getForm(), $cotizacion->getCliente());
            } else { //editando cotización, solo para validaciones
                $form
                    ->remove('cliente')
//                    ->remove('barco')
                    ->remove('fechaLlegada')
                    ->remove('fechaSalida')
                    ->remove('descuento')
                    ->remove('dolar')
                    ->remove('mensaje')
                    ->remove('mhcservicios')
                    ->remove('validacliente')
                    ->remove('notascliente')
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

<?php

namespace AppBundle\Form;


//use Doctrine\DBAL\Types\FloatType;
use AppBundle\Entity\MarinaHumedaCotizaServicios;
use AppBundle\Entity\Slip;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

class MarinaHumedaCotizacionGasolinaType extends AbstractType
{
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
                'required'=>false,
                'label' => 'Dolar',
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'MXN',
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
                'entry_type' => MarinaHumedaCotizaServiciosGasolinaType::class,
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
            ->add('notificarCliente', CheckboxType::class, [
                'label' => '¿Notificar al cliente?',
                'required' => false
            ])
            ->add('subtotal',MoneyType::class,[
                'required'=>false,
                'label' => 'Subtotal:',
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['class' => 'esdecimal tipo-letra1','autocomplete' => 'off','readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('ivatotal',MoneyType::class,[
                'required'=>false,
                'label' => 'I.V.A.:',
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['class' => 'esdecimal tipo-letra1','autocomplete' => 'off','readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ])
            ->add('total',MoneyType::class,[
                'required'=>false,
                'label' => 'Total:',
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['class' => 'esdecimal tipo-letra1','autocomplete' => 'off','readonly' => true],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ]);

//        $formModifier = function (FormInterface $form, Cliente $cliente = null) {
//            $barcos = null === $cliente ? array() : $cliente->getBarcos();
//
//            $form->add('barco', EntityType::class, array(
//                'class' => 'AppBundle:Barco',
//                'placeholder' => '',
//                'attr' => ['class' => 'busquedabarco'],
//                'choices' => $barcos,
//                'expanded' => true,
//                'multiple' => false
//            ));
//
//        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();

            if ($cotizacion->getId() == null) { //cotización nueva
                $form
                    ->remove('validanovo')
                    ->remove('validacliente')
                    ->remove('notasnovo')
                    ->remove('notascliente');
//                $formModifier($event->getForm(), $cotizacion->getCliente());
            } else { //editando cotización, solo para validaciones
                $form
                    ->remove('notificarCliente')
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

//        $builder->get('cliente')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($formModifier) {
//                // It's important here to fetch $event->getForm()->getData(), as
//                // $event->getData() will get you the client data (that is, the ID)
//                $cliente = $event->getForm()->getData();
//
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback functions!
//                $formModifier($event->getForm()->getParent(), $cliente);
//            }
//        );
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

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Cliente;

use AppBundle\Entity\MarinaHumedaCotizacionAdicional;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MarinaHumedaCotizacionAdicionalType extends AbstractType
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
            ->add('dolar',MoneyType::class,[
                'required'=>false,
                'attr' => ['class' => 'esdecimal', 'autocomplete'=>'off'],
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('iva',TextType::class,[
                'attr' => ['class' => 'esdecimal', 'autocomplete' => 'off'],
                'label' => 'IVA %'
            ])
            ->add('subtotal', MoneyType::class,[
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'label' => 'Sub-Total',
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['readonly' => true]
            ])
            ->add('ivatotal', MoneyType::class,[
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'label' => 'IVA',
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['readonly' => true]
            ])
            ->add('total', MoneyType::class,[
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'label_attr' => ['class' => 'letra-azul tipo-letra1'],
                'attr' => ['readonly' => true]
            ])
            ->add('mhcservicios',CollectionType::class,[
                'entry_type' => MarinaHumedaCotizaServiciosAdicionalesType::class,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('tipo', ChoiceType::class, [
                'choices' => array_flip(MarinaHumedaCotizacionAdicional::getTipoList())
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
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $form = $event->getForm();

                if($data->getId()){
                    $form
                        ->remove('cliente');

                }else{

                    $formModifier($event->getForm(), $data->getCliente());
                }

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
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizacionAdicional'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizacionadicional';
    }


}

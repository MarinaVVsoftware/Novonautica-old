<?php

namespace AppBundle\Form;

use AppBundle\Entity\Cliente;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
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
            ->add('dolar',TextType::class,[
                'attr' => ['class' => 'esdecimal',
                    'autocomplete'=>'off']
            ])
            ->add('mhcservicios',CollectionType::class,[
                'entry_type' => MarinaHumedaCotizaServiciosAdicionalesType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
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

<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AstilleroCotizacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('barco',EntityType::class,[
                'class' => 'AppBundle:Barco',
                'label' => 'Barco',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador selectbarcobuscar']
            ])
//            ->add('fechaLlegada',DateType::class,[
//                'label' => 'Fecha inicio',
//                'widget' => 'single_text',
//                'html5' => false,
//                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
//                'format' => 'yyyy-MM-dd',
//                'data' => new \DateTime(),
//            ])
//            ->add('fechaSalida',DateType::class,[
//                'label' => 'Fecha fin',
//                'widget' => 'single_text',
//                'html5' => false,
//                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
//                'format' => 'yyyy-MM-dd',
//                'data' => new \DateTime('+1 week'),
//            ])
//            ->add('diasEstadia',TextType::class,[
//                'label'=>'Días Estadia',
//                'attr' => ['class' => 'esnumero'],
//                'data' => '7'
//            ])
            ->add('dolar', MoneyType::class, [
                'required'=>false,
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
            ->add('acservicios',CollectionType::class,[
                'entry_type' => AstilleroCotizaServicioType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'attr' => ['prototype2' => ' '],
                'by_reference' => false
            ])
            ->add('notificarCliente', CheckboxType::class, [
                'label' => '¿Notificar al cliente?',
                'required' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();
            if ($cotizacion->getFechaLlegada() == null) {
                $form
                    ->add('fechaLlegada',DateType::class,[
                        'label' => 'Fecha inicio',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime(),
                    ])
                    ->add('fechaSalida',DateType::class,[
                        'label' => 'Fecha fin',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                        'data' => new \DateTime('+1 week'),
                    ])
                    ->add('diasEstadia',TextType::class,[
                        'label'=>'Días Estadia',
                        'attr' => ['class' => 'esnumero'],
                        'data' => '7'
                    ]);
            }else{
                $form
                    ->add('fechaLlegada',DateType::class,[
                        'label' => 'Fecha inicio',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ])
                    ->add('fechaSalida',DateType::class,[
                        'label' => 'Fecha fin',
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                        'format' => 'yyyy-MM-dd',
                    ])
                    ->add('diasEstadia',TextType::class,[
                        'label'=>'Días Estadia',
                        'attr' => ['class' => 'esnumero']
                    ]);
            }
        });

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AstilleroCotizacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocotizacion';
    }


}

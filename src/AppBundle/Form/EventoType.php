<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\DateTimeType;
//use Doctrine\DBAL\Types\TimeType;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo',TextType::class,[
                'label' => 'Título',
                'required' => false
            ])
            ->add('fechainicio',DateType::class,[
                'label' => 'Fecha inicio',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
            ])
            ->add('fechafin',DateType::class,[
            'label' => 'Fecha fin',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd'
                ])
            ->add('horainicio',TimeType::class,[
                'label' => 'Hora inicio',
                'html5' => 'false',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false
            ])
            ->add('horafin',TimeType::class,[
                'label' => 'Hora fin',
                'html5' => 'false',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false
            ])
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'attr' => ['rows' => 5],
                'required' => false
            ])


        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $evento = $event->getData();
            $form = $event->getForm();

            if($evento->getId() == null){
                $form
                    ->add('fondocolor',ColorType::class,[
                        'label' => 'Color de fondo',
                        'attr' => ['class' => 'input-color','value' => '#ffffff'],
                        'empty_data' => '#ffffff'
                    ])
                    ->add('letracolor',ColorType::class,[
                        'label' => 'Color de letra',
                        'attr' => ['class' => 'input-color', 'value' => '#000000'],
                        'empty_data' => '#000000'
                    ])
                    ->add('isPublico', ChoiceType::class, [
                        'choices' => ['Privado' => 0, 'Público' => 1],
                        'label' => 'Visibilidad',
                        'expanded' => true,
                        'multiple' => false,
                        'data' => 0
//                        'choice_attr' => function ($val, $key, $index) {return ['class' => 'opcion' . strtolower($key)];}
                        ]);
            }else{
                $form
                    ->add('fondocolor',ColorType::class,[
                        'label' => 'Color de fondo',
                        'attr' => ['class' => 'input-color'],
                        'empty_data' => '#ffffff'
                    ])
                    ->add('letracolor',ColorType::class,[
                        'label' => 'Color de letra',
                        'attr' => ['class' => 'input-color'],
                        'empty_data' => '#000000'
                    ])
                    ->add('isPublico', ChoiceType::class, [
                        'choices' => ['Privado' => 0, 'Público' => 1],
                        'label' => 'Visibilidad',
                        'expanded' => true,
                        'multiple' => false,
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
            'data_class' => 'AppBundle\Entity\Evento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_evento';
    }


}

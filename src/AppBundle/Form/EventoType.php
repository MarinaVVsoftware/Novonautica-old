<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\DateTimeType;
//use Doctrine\DBAL\Types\TimeType;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'format' => 'dd-MM-yyyy'
            ])
            ->add('fechafin',DateType::class,[
            'label' => 'Fecha fin',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'dd-MM-yyyy'
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
            ]);
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

<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\TextType;
//use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class BarcoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'label' => 'Nombre de la embarcación',
            ])
            ->add('modelo',TextType::class,[
            ])
            ->add('calado',TextType::class,[
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('manga',TextType::class,[
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('eslora',TextType::class,[
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('nombreResponsable',TextType::class,[
                'label' => 'Nombre del responsable',
                'required' => false
            ])
            ->add('telefonoResponsable',TextType::class,[
                'label' => 'Teléfono del responsable',
                'required' => false
            ])
            ->add('correoResponsable',TextType::class,[
                'label' => 'Correo del responsable',
                'required' => false
            ])
            ->add('nombreCapitan',TextType::class,[
                'label' => 'Nombre del capitán',
                'required' => false
            ])
            ->add('telefonoCapitan',TextType::class,[
                'label' => 'Teléfono del capitán',
                'required' => false
                ])
            ->add('correoCapitan',TextType::class,[
                'label' => 'Correo del capitán',
                'required' => false
            ])
            ->add('motores',CollectionType::class,[
                'entry_type' => MotorType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
        ;

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $barco = $event->getData();
//            $form = $event->getForm();
//
//            if($barco->getId()==null){ //cotización nueva
//                $form->remove('estatus');
//            }
//        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Barco'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_barco';
    }


}

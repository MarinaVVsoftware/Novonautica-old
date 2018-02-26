<?php

namespace AppBundle\Form;

use AppBundle\Entity\Cliente\RazonSocial;
use AppBundle\Form\Cliente\RazonSocialType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ClienteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('correo', EmailType::class)
            ->add('telefono', TextType::class, [
                'label' => 'Télefono',
                'required' => false
            ])
            ->add('celular', TextType::class, [
                'label' => 'Dirección',
                'required' => false
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false
            ])
            ->add('barcos', CollectionType::class, [
                'entry_type' => BarcoType::class,
                'label' => false,
                'entry_options' => ['label' => false]
            ])
            ->add('razonesSociales', CollectionType::class, [
                'entry_type' => RazonSocialType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente';
    }


}

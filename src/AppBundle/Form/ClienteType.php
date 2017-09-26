<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('nombre')
            ->add('correo')
            ->add('password')
            ->add('telefono', TextType::class,[
                'label'=>'Teléfono'
            ])
            ->add('celular')
            ->add('direccion',TextType::class,[
                'label'=>'Dirección'
            ])

            ->add('empresa')
            ->add('razonsocial',TextType::class,[
                'label'=>'Razón Social'
            ])
            ->add('rfc',TextType::class,[
                'label'=>'RFC'
            ])
            ->add('direccionfiscal',TextType::class,[
                'label'=>'Dirección facturación'
            ])
            ->add('correofacturacion',TextType::class,[
                'label'=>'Correo Facturación'
            ])
            ->add('estatus',null,[
                'label'=>' '
            ])
            ->add('barcos',CollectionType::class,[
                'entry_type' => BarcoType::class,
                'label' => false
            ])
        ;
 
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

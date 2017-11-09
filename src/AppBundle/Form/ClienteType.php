<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('nombre',TextType::class,[
                'required' => false
            ])
            ->add('correo',TextType::class,[
                'required' => false
            ])
            ->add('telefono', TextType::class,[
                'label'=>'Teléfono',
                'required' => false
            ])
            ->add('celular',TextType::class,[
                'required' => false
            ])
            ->add('direccion',TextType::class,[
                'label'=>'Dirección',
                'required' => false
            ])
            ->add('empresa',TextType::class,[
                'required' => false
            ])
            ->add('razonsocial',TextType::class,[
                'label'=>'Razón Social',
                'required' => false
            ])
            ->add('rfc',TextType::class,[
                'label'=>'RFC',
                'required' => false
            ])
            ->add('direccionfiscal',TextType::class,[
                'label'=>'Dirección facturación',
                'required' => false
            ])
            ->add('correofacturacion',TextType::class,[
                'label'=>'Correo Facturación',
                'required' => false
            ])
//            ->add('estatus',null,[
//                'label'=>' ',
//                'required' => false
//            ])
            ->add('barcos',CollectionType::class,[
                'entry_type' => BarcoType::class,
                'label' => false
            ])
        ;
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $cliente = $event->getData();
//            $form = $event->getForm();
//
//            if($cliente->getId()==null){ //cotización nueva
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

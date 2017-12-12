<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ValorSistemaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dolar',MoneyType::class,[
                'required'=>false,
                'attr' => ['class' => 'esdecimal'],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('iva',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
                'required' => false
            ])
            ->add('folioMarina',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
                'label' => 'Folio Marina Húmeda Cotización'
            ])
            ->add('diasHabilesMarinaCotizacion',TextType::class,[
                'label' => 'Días hábiles pago de cotización marina húmeda',
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
            ])
            ->add('mensajeCorreoMarina',TextareaType::class,[
                'attr' => ['rows' => 7, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Mensaje en correo de marina húmeda cotización'
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ValorSistema'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_valorsistema';
    }


}

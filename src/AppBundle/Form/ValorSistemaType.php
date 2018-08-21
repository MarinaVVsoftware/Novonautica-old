<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
                'currency' => 'MXN',
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
                'label' => 'Folio marina y astillero'
            ])
            ->add('folioCombustible',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
                'label' => 'Folio combustible'
            ])
            ->add('diasHabilesMarinaCotizacion',TextType::class,[
                'label' => 'Días hábiles cotización marina húmeda',
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
            ])
            ->add('diasHabilesAstilleroCotizacion',TextType::class,[
                'label' => 'Días hábiles cotización astillero',
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
            ])
            ->add('diasHabilesCombustible',TextType::class,[
                'label' => 'Días hábiles cotización combustible',
                'attr' => ['class' => 'esdecimal'],
                'required' => false,
            ])
            ->add('mensajeCorreoMarina',TextareaType::class,[
                'attr' => ['rows' => 10, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Mensaje en correo de marina húmeda cotización'
            ])
            ->add('mensajeCorreoMarinaGasolina',TextareaType::class,[
                'attr' => ['rows' => 10, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Mensaje en correo de gasolina de marina húmeda cotización'
            ])
            ->add('mensajeCorreoAstillero',TextareaType::class,[
                'attr' => ['rows' => 10, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Mensaje en correo de astillero cotización'
            ])
            ->add('porcentajeMoratorio',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
                'label' => 'Porcentaje moratorio (marina húmeda)',
                'required' => false
            ])
            ->add('direccion', TextType::class,[
                'label' => 'Dirección',
            ])
            ->add('codigoPostal', TextType::class,[
                'label' => 'Código Postal',
                'required' => false
            ])
            ->add('telefono',TextType::class,[
                'label' => 'Teléfono'
            ])
            ->add('correo',EmailType::class)
            ->add('terminosMarina',TextareaType::class,[
                'attr' => ['rows' => 15, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Términos y condiciones marina húmeda'
            ])
            ->add('terminosAstillero', TextareaType::class,[
                'attr' => ['rows' => 15, 'class' => 'editorwy'],
                'required' => false,
                'label' => 'Términos y condiciones astillero'
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

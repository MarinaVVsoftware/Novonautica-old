<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlipMovimientoLockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nota', TextType::class, ['required' => true])
            ->add('fechaLlegada', DateType::class, [
                'required' => true,
                'html5' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-calendario',
                    'readonly' => 'readonly',
                ],
                'format' => 'dd/MM/yyyy',
            ])
            ->add('fechaSalida', DateType::class, [
                'required' => true,
                'html5' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-calendario',
                    'readonly' => 'readonly',
                ],
                'format' => 'dd/MM/yyyy',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SlipMovimiento',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_slipmovimiento_lock';
    }


}

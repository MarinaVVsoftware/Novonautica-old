<?php

namespace AppBundle\Form\Cliente;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RazonSocialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rfc', TextType::class, ['label' => 'RFC'])
            ->add('razonSocial', TextType::class, ['label' => 'Razón Social'])
            ->add('direccion', TextType::class, ['label' => 'Dirección'])
            ->add('correos', TextType::class, ['label' => 'Correos de recepción (separados por comas)']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente\RazonSocial'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente_razonsocial';
    }


}

<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmbarcacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('precio')
            ->add('construccion', ChoiceType::class, [
                'choices' => [
                    'Prefabricado' => 0,
                    'Custom' => 1
                ]
            ])
            ->add('marca', EntityType::class, [
                'class' => 'AppBundle\Entity\EmbarcacionMarca',
            ])
            ->add('ano', TextType::class, [
                'label' => 'Año'
            ])
            ->add('longitud')
            ->add('eslora')
            ->add('manga')
            ->add('calado')
            ->add('peso')
            ->add('capacidadCombustible')
            ->add('capacidadAgua')
            ->add('capacidadDeposito')
            ->add('cabinas')
            ->add('pasajerosDormidos')
            ->add('generador')
            ->add('descripcion');


    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Embarcacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_embarcacion';
    }


}

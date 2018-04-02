<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AstilleroServicioBasicoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('precio', MoneyType::class, [
                'required'=>false,
                'attr' => ['class' => 'esdecimal','autocomplete' => 'off'],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
            ])
//            ->add('divisa',ChoiceType::class,[
//                'choices'  => ['USD' => 'USD','MXN' => 'MXN'],
//                'label' => 'Divisa',
//            ])
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AstilleroServicioBasico'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astilleroserviciobasico';
    }


}

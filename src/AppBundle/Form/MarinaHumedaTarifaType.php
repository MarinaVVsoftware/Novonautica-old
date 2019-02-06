<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarinaHumedaTarifaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo',ChoiceType::class,[
                'choices' =>[
                            'Amarre' => 1,
                            'Electricidad' => 2
                            ]
            ])
            ->add('costo',MoneyType::class,[
                'label' => 'Costo por día (USD)',
                'required' => false,
                'attr' => ['class' => 'esdecimal'],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
            ])

            ->add('descripcion',TextType::class,[
                'label' => 'Descripción',
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MarinaHumedaTarifa'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedatarifa';
    }
}

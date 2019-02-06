<?php

namespace AppBundle\Form;

use AppBundle\Entity\MarinaHumedaTarifa;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['rows' => '4']
            ])
            ->add('condicion', ChoiceType::class, [
                'choices' => array_flip(MarinaHumedaTarifa::getCondicionList())
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
           $configuracion = $event->getData()->getId() === null
               ? ['label' => 'Pies', 'attr' => ['class' => 'esdecimal'],'required' => false,'empty_data' => 0,'data' => 0]
               : ['label' => 'Pies', 'attr' => ['class' => 'esdecimal'],'required' => false,'empty_data' => 0];
           $event->getForm()
                ->add('piesA',TextType::class,$configuracion)
                ->add('piesB', TextType::class,$configuracion);
        });

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

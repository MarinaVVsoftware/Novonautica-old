<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Cliente;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AstilleroCotizacionValidarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('validanovo', ChoiceType::class, [
                'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function ($val, $key, $index) {
                    return ['class' => 'opcion' . strtolower($key)];
                },
            ])
            ->add('notasnovo', TextareaType::class, [
                'label' => 'Observaciones',
                'attr' => ['rows' => 7],
                'required' => false
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $cotizacion = $event->getData();
            $form = $event->getForm();

            if($cotizacion->getValidanovo() == 2) {
                $form
                    ->remove('validanovo')
                    ->remove('notasnovo')
                    ->add('validacliente', ChoiceType::class, [
                        'choices' => ['Aceptar' => 2, 'Rechazar' => 1],
                        'expanded' => true,
                        'multiple' => false,
                        'choice_attr' => function ($val, $key, $index) {
                            return ['class' => 'opcion' . strtolower($key)];
                        },
                    ])
                    ->add('notascliente', TextareaType::class, [
                        'label' => 'Observaciones',
                        'attr' => ['rows' => 7],
                        'required' => false
                    ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AstilleroCotizacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocotizacion';
    }


}

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Embarcacion;
use AppBundle\Entity\EmbarcacionMarca;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmbarcacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('precio', MoneyType::class, [
                'label' => 'Precio (USD)',
                'currency' => 'USD',
                'grouping' => true
            ])
            ->add('construccion', ChoiceType::class, [
                'choices' => [
                    'Prefabricado' => 'prefabricado',
                    'Custom' => 'custom',
                ]
            ])
            ->add('marca', EntityType::class, [
                'class' => 'AppBundle\Entity\EmbarcacionMarca',
            ])
            ->add('ano', TextType::class, [
                'label' => 'Año'
            ])
            ->add('builder')
            ->add('interiorDesigner')
            ->add('exteriorDesigner')
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
            ->add('descripcion')
            ->add('video');

        $formModifier = function (FormInterface $form, EmbarcacionMarca $marca = null) {
            $modelos = $marca ? $marca->getModelos() : [];

            $form->add('modelo', EntityType::class, [
                'class' => 'AppBundle\Entity\EmbarcacionModelo',
                'choices' => $modelos
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Embarcacion $embarcacion */
                $embarcacion = $event->getData();
                $formModifier($event->getForm(), $embarcacion->getMarca());
            });

        $builder->addEventListener(FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                /** @var Embarcacion $embarcacion */
                $embarcacion = $event->getData();

                if ($embarcacion->getConstruccion() === 'custom') {
                    $embarcacion->setMarca(null);
                    $embarcacion->setModelo(null);
                } else {
                    $embarcacion->setBuilder(null);
                    $embarcacion->setInteriorDesigner(null);
                    $embarcacion->setExteriorDesigner(null);
                }
            });

        $builder->get('marca')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $marca = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $marca);
            });
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

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Embarcacion;
use AppBundle\Entity\EmbarcacionMarca;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
                'grouping' => true,
                'divisor' => 100
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
                'label' => 'Año',
                'attr' => ['placeholder' => '2017']
            ])
            ->add('builder')
            ->add('interiorDesigner')
            ->add('exteriorDesigner')
            ->add('longitud', NumberType::class, [
                'label' => 'Longitud (pies)',
                'attr' => ['placeholder' => '63.7']
            ])
            ->add('eslora', NumberType::class, [
                'label' => 'Eslora (pies)',
                'attr' => ['placeholder' => '59.7']
            ])
            ->add('manga', NumberType::class, [
                'label' => 'Manga (pies)',
                'attr' => ['placeholder' => '17']
            ])
            ->add('calado', NumberType::class, [
                'label' => 'Calado (pies)',
                'attr' => ['placeholder' => '4.7']
            ])
            ->add('peso', NumberType::class, [
                'label' => 'Peso (kg)',
                'attr' => ['placeholder' => '29,000']
            ])
            ->add('capacidadCombustible', NumberType::class, [
                'label' => 'Capacidad de combustible (litros)',
                'attr' => ['placeholder' => '4,200']
            ])
            ->add('capacidadAgua', NumberType::class, [
                'label' => 'Capacidad de agua (litros)',
                'attr' => ['placeholder' => '800']
            ])
            ->add('capacidadDeposito', NumberType::class, [
                'label' => 'Capacidad de deposito (litros)',
                'attr' => ['placeholder' => '300']
            ])
            ->add('cabinas', NumberType::class, [
                'label' => 'Cabinas',
                'attr' => ['placeholder' => '3']
            ])
            ->add('pasajerosDormidos', NumberType::class, [
                'label' => 'Max. pasajeros por cabina',
                'attr' => ['placeholder' => '6']
            ])
            ->add('generador', TextType::class, [
                'attr' => ['placeholder' => 'CUMMINS ONAN 17.5 KW']
            ])
            ->add('descripcion')
            ->add('video', TextType::class, [
                'label' => 'Video (Youtube)',
                'attr' => ['placeholder' => 'https://www.youtube.com/watch?v=SstAlDGCcIk'],
                'required' => false,
            ])
            ->add('imagenes', CollectionType::class, [
                'entry_type' => EmbarcacionImagenType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('brochureFile', VichFileType::class, [
                'required' => false,
                'label' => 'Brochure',
                'download_label' => 'Ver brochure',
                'delete_label' => '¿Eliminar brochure?'
            ])
            ->add('layouts', CollectionType::class, [
                'entry_type' => EmbarcacionLayoutType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('motores', CollectionType::class, [
                'label' => false,
                'entry_type' => EmbarcacionMotorType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;

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

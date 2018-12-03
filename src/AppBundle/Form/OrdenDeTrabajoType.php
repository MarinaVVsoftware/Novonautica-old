<?php

namespace AppBundle\Form;

use AppBundle\Form\Astillero\ContratistaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenDeTrabajoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('astilleroCotizacion', EntityType::class, [
                'class' => 'AppBundle\Entity\AstilleroCotizacion',
                'label' => 'Cotización',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador buscarfolio'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ac')
                        ->select('ac', 'odt')
                        ->leftJoin('ac.odt', 'odt')
                        ->andWhere('ac.validacliente = 2')
                        ->andWhere('ac.estatus = 1')
                        ->andWhere('odt.astilleroCotizacion IS NULL')
                        ->orderBy('ac.folio');
                }
            ])
            ->add('contratistas', CollectionType::class, [
                'entry_type' => ContratistaType::class,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $odt = $event->getData();
            $form = $event->getForm();

            if($odt->getId() ){
                $form
                    ->remove('astilleroCotizacion');
            }

        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenDeTrabajo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordendetrabajo';
    }


}

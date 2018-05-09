<?php

namespace AppBundle\Form\Cliente;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Cliente\Notificacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificacionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('folioCotizacion')
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'attr' => ['class' => 'select-buscador'],
            ])
            ->add('tipo', ChoiceType::class, [
                'choices' => array_flip(Notificacion::getTipoList()),
            ])
            ->add('fechaNotificacionCobro', DateTimeType::class, [
                'label' => 'Fecha vencimiento de mensualidad',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker-solo input-calendario',
                    'readonly' => true
                ],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime()
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente\Notificacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente_notificacion';
    }


}

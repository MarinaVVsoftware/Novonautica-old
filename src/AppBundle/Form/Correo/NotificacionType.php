<?php

namespace AppBundle\Form\Correo;

use AppBundle\Entity\Correo\Notificacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('correo', EmailType::class)
            ->add('evento', ChoiceType::class, [
                'choices' => array_flip(Notificacion::getEventoList())
            ])
            ->add('tipo', ChoiceType::class, [
                'choices' => array_flip(Notificacion::getTipoList())
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Correo\Notificacion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_correo_notificacion';
    }


}

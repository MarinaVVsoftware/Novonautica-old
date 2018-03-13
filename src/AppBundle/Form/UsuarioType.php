<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('nombreUsuario')
            ->add('correo', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'label' => 'Modulos',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Clientes' => 'ROLE_CLIENTE',
                    'Marina Humeda' => 'ROLE_MARINA',
                    'Astillero' => 'ROLE_ASTILLERO',
                    'Ocean Deal' => 'ROLE_EMBARCACION',
                    'Tienda' => 'ROLE_TIENDA',
                    'Contabilidad' => 'ROLE_CONTABILIDAD',
                    'Correos' => 'ROLE_CORREOS',
                    'Recursos Humanos' => 'ROLE_RH',
                    'Ajustes' => 'ROLE_AJUSTES',
                ]
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Estatus',
                'choices' => [
                    'Activo' => true,
                    'Inactivo' => false
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $usuario = $event->getData();
            $form = $event->getForm();

            if ($usuario->getId()) {
                $form->add('plainPassword', PasswordType::class, [
                    'label' => 'Contraseña',
                    'required' => false
                ]);
            } else {
                $form->add('plainPassword', PasswordType::class, [
                    'label' => 'Contraseña',
                    'constraints' => [
                        new NotBlank()
                    ]
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
            'data_class' => 'AppBundle\Entity\Usuario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_usuario';
    }


}

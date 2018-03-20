<?php

namespace AppBundle\Form;

use AppBundle\Repository\PermissionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Clientes' => [
                        'Acceso' => 'ROLE_CLIENTE',
                        'Crear' => 'CLIENTE_CREATE',
                        'Editar' => 'CLIENTE_EDIT',
                    ],
                    'Marina' => [
                        'Acceso' => 'ROLE_MARINA',
                        'Crear' => 'MARINA_COTIZACION_CREATE',
                        'Validar' => 'MARINA_COTIZACION_VALIDATE',
                        'Renovar' => 'MARINA_COTIZACION_RENEW',
                        'Recotizar' => 'MARINA_COTIZACION_REQUOTE',
                        'Pagos' => 'ROLE_MARINA_PAGO',
                        'Monedero' => 'ROLE_MARINA_MONEDERO',
                        'Slips' => 'ROLE_MARINA_SLIP',
                        'Tarifas' => 'ROLE_MARINA_TARIFF',
                        'Servicios adicionales' => 'ROLE_MARINA_SERVICIOADICIONAL',
                    ],
                    'Astillero' => [
                        'Acceso' => 'ROLE_ASTILLERO',
                        'Crear' => 'ASTILLERO_COTIZACION_CREATE',
                        'Validar' => 'ASTILLERO_COTIZACION_VALIDATE',
                        'Recotizar' => 'ASTILLERO_COTIZACION_REQUOTE',
                        'Pagos' => 'ROLE_ASTILLERO_PAGO',
                        'ODT' => 'ROLE_ASTILLERO_ODT',
                        'Productos' => 'ROLE_ASTILLERO_PRODUCTO',
                        'Servicios' => 'ROLE_ASTILLERO_SERVICIO',
                    ],
                    'Ocean Deal' => [
                        'Acceso' => 'ROLE_EMBARCACION',
                    ],
                    'Tienda' => [
                        'Acceso' => 'ROLE_TIENDA'
                    ],
                    'Contabilidad' => [
                        'Acceso' => 'ROLE_CONTABILIDAD'
                    ],
                    'Historial de Correos' => [
                        'Acceso' => 'ROLE_HCORREO'
                    ],
                    'Recursos humanos' => [
                        'Acceso' => 'ROLE_RH'
                    ],
                    'Ajustes' => [
                        'Acceso' => 'ROLE_AJUSTES'
                    ],
                ]
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Estatus',
                'choices' => ['Activo' => true, 'Inactivo' => false]
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

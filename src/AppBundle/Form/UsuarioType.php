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
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Clientes' => [
                        'Acceso' => 'ROLE_CLIENTE',
                        'Crear' => 'CLIENTE_CREATE',
                        'Modificar' => 'CLIENTE_EDIT',
                        'Eliminar' => 'CLIENTE_DELETE',
                    ],
                    'Marina' => [
                        'Acceso' => 'ROLE_MARINA',
                        'Crear' => 'MARINA_COTIZACION_CREATE',
                        'Validar' => 'MARINA_COTIZACION_VALIDATE',
                        'Renovar' => 'MARINA_COTIZACION_RENEW',
                        'Recotizar' => 'MARINA_COTIZACION_REQUOTE',
                        'Eliminar' => 'MARINA_COTIZACION_DELETE',
                        'Pagos' => 'ROLE_MARINA_PAGO',
                        'Moratoria' => 'MARINA_COTIZACION_MORATORIA',
                        'Monedero' => 'ROLE_MARINA_MONEDERO',
                        'Slips' => 'ROLE_MARINA_SLIP',
                        'Tarifas' => 'ROLE_MARINA_TARIFF',
                        'Servicios adicionales' => 'ROLE_MARINA_SERVICIOADICIONAL',
                    ],
                    'Astillero' => [
                        'Acceso' => 'ROLE_ASTILLERO',
                        'Crear' => 'ASTILLERO_COTIZACION_CREATE',
                        'Eliminar' => 'ASTILLERO_COTIZACION_DELETE',
                        'Validar' => 'ASTILLERO_COTIZACION_VALIDATE',
                        'Recotizar' => 'ASTILLERO_COTIZACION_REQUOTE',
                        'Pagos' => 'ROLE_ASTILLERO_PAGO',
                        'Productos' => 'ROLE_ASTILLERO_PRODUCTO',
                        'Servicios' => 'ROLE_ASTILLERO_SERVICIO',
                        'Proveedores' => 'ROLE_ASTILLERO_PROVEEDOR',
                        'Servicios básicos' => 'ROLE_ASTILLERO_SERVICIOBASICO',
                        'Responsable' => 'ROLE_ASTILLERO_RESPONSABLE',
                    ],
                    'Reporte' => [
                        'Acceso' => 'ROLE_REPORTE',
                        'Marina' => 'ROLE_REPORTE_MARINA',
                        'Astillero' => 'ROLE_REPORTE_ASTILLERO',
                    ],
                    'ODT' => [
                        'Acceso' => 'ROLE_ODT',
                        'Crear' => 'ROLE_ODT_CREATE',

                        'Actividad' => 'ROLE_ODT_ACTIVIDAD',
                        'Editar contratista' => 'ROLE_ODT_CONTRATISTA_EDIT',
                        'Eliminar' => 'ROLE_ODT_DELETE',
                    ],
                    'Ocean Deal' => [
                        'Acceso' => 'ROLE_EMBARCACION',
                        'Crear' => 'EMBARCACION_CREATE',
                        'Modificar' => 'EMBARCACION_EDIT',
                        'Eliminar' => 'EMBARCACION_DELETE',
                        'Marcas' => 'ROLE_EMBARCACION_MARCA',
                        'Modelos' => 'ROLE_EMBARCACION_MODELO',
                    ],
                    'V&V Store' => [
                        'Acceso' => 'ROLE_TIENDA',
                        'Crear' => 'TIENDA_CREATE',
                        'Productos' => 'ROLE_TIENDA_PRODUCTO',
                        'Punto de venta' => 'ROLE_TIENDA_POV',
                        'Inventario' => 'ROLE_TIENDA_INVENTARIO',
                        'Registros' => 'ROLE_TIENDA_REGISTRO',
                    ],
                    'Contabilidad' => [
                        'Acceso' => 'ROLE_CONTABILIDAD',
                        'Crear' => 'CONTABILIDAD_CREATE',
                        'Cancelar' => 'CONTABILIDAD_CANCEL',
                        'Emisores' => 'ROLE_CONTABILIDAD_EMISOR',
                        'Pagos ODT' => 'ROLE_ODT_PAGO',
                        'Egresos' => 'ROLE_CONTABILIDAD_EGRESO'
                    ],
                    'Correos' => [
                        'Acceso' => 'ROLE_HCORREO',
                        'Notificaciones' => 'ROLE_HCORREO_NOTIFICACION',
                    ],
                    'Recursos humanos' => [
                        'Acceso' => 'ROLE_RH',
                        'Crear' => 'RH_CREATE',
                        'Modificar' => 'RH_EDIT',
                        'Eliminar' => 'RH_DELETE',
                    ],
                    'Ajustes' => [
                        'Acceso' => 'ROLE_AJUSTES',
                        'Cuentas bancarias' => 'ROLE_AJUSTES_CUENTAS_BANCARIAS',
                    ],
                    'Agenda' => [
                        'Acceso' => 'ROLE_AGENDA',
                        'Crear' => 'AGENDA_CREATE',
                        'Modificar' => 'AGENDA_EDIT',
                        'Eliminar' => 'AGENDA_DELETE'
                    ]
                ]
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'Estatus',
                'choices' => ['Activo' => true, 'Inactivo' => false],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $usuario = $event->getData();
            $form = $event->getForm();

            if ($usuario->getId()) {
                $form->add('plainPassword', PasswordType::class, [
                    'label' => 'Contraseña',
                    'required' => false,
                ]);
            } else {
                $form->add('plainPassword', PasswordType::class, [
                    'label' => 'Contraseña',
                    'constraints' => [
                        new NotBlank(),
                    ],
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
            'data_class' => 'AppBundle\Entity\Usuario',
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

<?php

namespace AppBundle\Form;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('nombreUsuario');
        $builder->add('correo', EmailType::class);
        $builder->add('roles', ChoiceType::class, [
            'label' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => [
                'Clientes' => [
                    'Acceso' => 'ROLE_CLIENTE',
                    'Crear' => 'CLIENTE_CREATE',
                    'Modificar' => 'CLIENTE_EDIT',
                    'Eliminar' => 'CLIENTE_DELETE',
                    'Ver contacto' => 'ROLE_CLIENTE_VER_CONTACTO'
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
                'Combustible' => [
                    'Acceso' => 'ROLE_COMBUSTIBLE',
                    'Crear' => 'COMBUSTIBLE_COTIZACION_CREATE',
                    'Validar' => 'COMBUSTIBLE_COTIZACION_VALIDATE',
                    'Recotizar' => 'COMBUSTIBLE_COTIZACION_REQUOTE',
                    'Eliminar' => 'COMBUSTIBLE_COTIZACION_DELETE',
                    'Pagos' => 'ROLE_COMBUSTIBLE_PAGO',
                    'Catálogo' => 'ROLE_COMBUSTIBLE_CATALOGO'
                ],
                'Astillero' => [
                    'Acceso' => 'ROLE_ASTILLERO',
                    'Crear' => 'ASTILLERO_COTIZACION_CREATE',
                    'Eliminar' => 'ASTILLERO_COTIZACION_DELETE',
                    'Validar' => 'ASTILLERO_COTIZACION_VALIDATE',
                    'Recotizar' => 'ASTILLERO_COTIZACION_REQUOTE',
                    'Pagos' => 'ROLE_ASTILLERO_PAGO',
                    'Monedero' => 'ROLE_ASTILLERO_MONEDERO',
                    'Productos' => 'ROLE_ASTILLERO_PRODUCTO',
                    'Servicios' => 'ROLE_ASTILLERO_SERVICIO',
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
                    'Ver productos' => 'ROLE_TIENDA_PRODUCTO',
                    'Agregar productos' => 'TIENDA_PRODUCTO_CREATE',
                    'Gerente de punto de venta' => 'ROLE_ADMIN_POV',
                    'Punto de venta' => 'ROLE_TIENDA_POV',
                ],
                'JRF Marine' => [
                    'Acceso' => 'ROLE_JRF',
                    'Productos' => 'ROLE_JRF_PRODUCTO',
                    'Marcas' => 'ROLE_JRF_MARCA',
                    'Categorias' => 'ROLE_JRF_CATEGORIA',
                    'Subcategorias' => 'ROLE_JRF_SUBCATEGORIA',
                ],
                'Contabilidad' => $this->getContabilidadRoles(),
                'Proveedores' =>[
                    'Acceso' => 'ROLE_PROVEEDOR',
                    'Crear' => 'PROVEEDOR_CREATE',
                    'Modificar' => 'PROVEEDOR_EDIT',
                    'Eliminar' => 'PROVEEDOR_DELETE',
                    'Oficios' => 'ROLE_OFICIO'
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
                    'Eliminar' => 'AGENDA_DELETE',
                ],
                'Solicitud' => $this->getSolicitudRoles(),
                'Compra' => [
                    'Acceso' => 'ROLE_COMPRA',
                    'Modificar' => 'COMPRA_EDIT',
                    'Validar' => 'COMPRA_VALIDAR'
                ],
                'Almacén' => [
                    'Acceso' => 'ROLE_ALMACEN',
                    'Validar' => 'ALMACEN_VALIDAR',
                    'Inventario' => 'ROLE_INVENTARIO',
                    'Modificación de Inventario' => 'ROLE_INVENTARIO_MODIFICAR',
                ]

            ],
        ]);

        $builder->add('isActive', ChoiceType::class, [
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

    private function getContabilidadRoles()
    {
        $emisorRepository = $this->entityManager->getRepository(Emisor::class);
        $roles = [
            'Acceso' => 'ROLE_CONTABILIDAD',
            'Crear' => 'CONTABILIDAD_CREATE',
            'Cancelar' => 'CONTABILIDAD_CANCEL',
            'Emisores' => 'ROLE_CONTABILIDAD_EMISOR',
            'Pagos ODT' => 'ROLE_ODT_PAGO',
            'Egresos' => 'ROLE_CONTABILIDAD_EGRESO',
            'Editar egreso' => 'EGRESO_EDIT'
        ];

        foreach ($emisorRepository->getEmisorRoles() as $emisorRole) {
            $alias = join('-', explode(' ', $emisorRole['alias']));
            $roles[$emisorRole['alias']] = "VIEW_EGRESO_{$alias}_{$emisorRole['id']}";
        }

        return $roles;
    }
    private function getSolicitudRoles()
    {
        $emisorRepository = $this->entityManager->getRepository(Emisor::class);
        $roles = [
            'Acceso' => 'ROLE_SOLICITUD',
            'Crear' => 'SOLICITUD_CREATE',
            'Modificar' => 'SOLICITUD_EDIT',
            'Eliminar' => 'SOLICITUD_DELETE'
        ];
        foreach ($emisorRepository->getEmisorRoles() as $emisorRole) {
            $alias = join('-', explode(' ', $emisorRole['alias']));
            $roles[$emisorRole['alias']] = "VIEW_SOLICITUD_{$alias}_{$emisorRole['id']}";
        }
        return $roles;
    }
}

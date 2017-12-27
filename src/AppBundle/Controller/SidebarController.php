<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/15/17
 * Time: 11:48
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SidebarController extends Controller
{
    private $paths = [];

    public function createAction()
    {
        $this->user();
        $this->admin();
        $requestStack = $this->get('request_stack')->getMasterRequest();

        return $this->render(':estructura:sidebar.html.twig', [
            'menus' => $this->paths,
            'current_path' => $requestStack->getBaseUrl() . $requestStack->getPathInfo()
        ]);
    }

    private function user()
    {
        $this->add([
            [
                'name' => 'Clientes',
                'icon' => 'users',
                'path' => $this->generateUrl('cliente_index'),
                'submenu' => [
                    ['name' => 'Nuevo', 'path' => $this->generateUrl('cliente_new')],
                    ['name' => 'Listado', 'path' => $this->generateUrl('cliente_index')]
                ]
            ],
            [
                'name' => 'Marina Humeda',
                'path' => $this->removeOneRoute($this->removeOneRoute($this->generateUrl('marina-administracion'))),
                'icon' => 'ship',
                'submenu' => [
                    [
                        'name' => 'Slip',
                        'path' => $this->removeOneRoute($this->generateUrl('marina-administracion')),
                        'submenu' => [
                            ['name' => 'Mapa', 'path' => $this->generateUrl('marina-administracion')],
                            ['name' => 'Ocupacion', 'path' => $this->generateUrl('slipmovimiento_index')],
                        ]
                    ],
                    ['name' => 'Monedero', 'path' => $this->generateUrl('mh_monedero_index')],
                    [
                        'name' => 'Cotizaciones',
                        'path' => $this->generateUrl('marina-humeda_index'),
                        'submenu' => [
                            [
                                'name' => 'Estadía',
                                'path' => $this->generateUrl('marina-humeda_estadia_index'),
                                'submenu' => [
                                    ['name' => 'Listado', 'path' => $this->generateUrl('marina-humeda_estadia_index')],
                                    ['name' => 'Nuevo', 'path' => $this->generateUrl('marina-humeda_estadia_new')],
                                    ['name' => 'Tarifas', 'path' => $this->generateUrl('marinahumeda-tarifas_index')],
                                ]
                            ],
                            [
                                'name' => 'Gasolina',
                                'path' => $this->generateUrl('marina-humeda_gasolina_index'),
                                'submenu' => [
                                    ['name' => 'Listado', 'path' => $this->generateUrl('marina-humeda_gasolina_index')],
                                    ['name' => 'Nuevo', 'path' => $this->generateUrl('marina-humeda_gasolina_new')],
                                ]
                            ],
                        ]
                    ],
                    [
                        'name' => 'Servicios adicionales',
                        'path' => $this->generateUrl('marina-humeda-cotizacion-adicional_index'),
                        'submenu' => [
                            ['name' => 'Listado', 'path' => $this->generateUrl('marina-humeda-cotizacion-adicional_index')],
                            ['name' => 'Nuevo', 'path' => $this->generateUrl('marina-humeda-cotizacion-adicional_new')],
                            ['name' => 'Catálogo', 'path' => $this->generateUrl('marina-humeda-servicio_index')]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Astillero',
                'icon' => 'anchor',
                'path' => $this->generateUrl('astillero_index'),
                'submenu' => [
                    ['name' => 'Nueva cotización', 'path' => $this->generateUrl('astillero_new')],
                    ['name' => 'Cotizaciones', 'path' => $this->generateUrl('astillero_index')],
                    ['name' => 'Aceptaciones', 'path' => $this->generateUrl('astillero-aceptaciones')],
                    ['name' => 'ODT', 'path' => $this->generateUrl('astillero-odt')],
                ]
            ],
            [
                'name' => 'Ocean Deal',
                'icon' => 'life-buoy',
                'path' => $this->generateUrl('embarcacion_index'),
                'submenu' => [
                    ['name' => 'Embarcaciones', 'path' => $this->generateUrl('embarcacion_index')],
                    ['name' => 'Nueva embarcación', 'path' => $this->generateUrl('embarcacion_new')],
                    ['name' => 'Marcas', 'path' => $this->generateUrl('embarcacion_marca_new')],
                    ['name' => 'Modelos', 'path' => $this->generateUrl('embarcacion_modelo')]
                ]
            ],
            ['name' => 'Eventos', 'icon' => 'address-book', 'path' => $this->generateUrl('marina-agenda')],
            [
                'name' => 'Productos',
                'icon' => 'th',
                'path' => $this->generateUrl('producto_index'),
                'submenu' => [
                    ['name' => 'Listado', 'path' => $this->generateUrl('producto_index')],
                    ['name' => 'Nuevo Producto', 'path' => $this->generateUrl('producto_new')],
                    ['name' => 'Marcas', 'path' => $this->generateUrl('producto_marca')],
                    ['name' => 'Categorias', 'path' => $this->generateUrl('producto_categoria')],
                    ['name' => 'Subcategorias', 'path' => $this->generateUrl('producto_subcategoria')],
                ]
            ],
            ['name' => 'Recursos Humanos', 'icon' => 'address-book-o', 'path' => $this->generateUrl('recursos-humanos')],
            [
                'name' => 'Contabilidad',
                'icon' => 'archive',
                'path' => $this->generateUrl('contabilidad'),
                'submenu' => [
                    ['name' => 'Facturas', 'path' => $this->generateUrl('display_cotizacion_facturacion')],
                    ['name' => 'Nueva factura', 'path' => $this->generateUrl('display_new_cotizacion_facturacion')]
                ]
            ],
            ['name' => 'Reportes', 'icon' => 'file-text-o', 'path' => $this->generateUrl('reportes')],
        ]);
    }

    private function admin()
    {
        $this->add([
//            ['name' => 'Usuarios', 'icon' => 'user-o', 'path' => $this->generateUrl('usuario_index')],
//            ['name' => 'Ajustes', 'icon' => 'cog', 'path' => $this->generateUrl('ajustes_index')],
            [
                'name' => 'Ajustes',
                'icon' => 'cog',
                'path' => $this->generateUrl('ajustes_index'),
                'submenu' => [
                    ['name' => 'Valores', 'path' => $this->generateUrl('ajustes_index')],
                    ['name' => 'Cuentas bancarias', 'path' => $this->generateUrl('cuenta-bancaria_index')],
                ]
            ]
        ]);
    }

    private function add(Array $paths): array
    {
        return $this->paths = array_merge($this->paths, $paths);
    }

    private function removeOneRoute($route) : string
    {
        return implode('/', array_slice(explode('/', $route), 1, -1));
    }
}
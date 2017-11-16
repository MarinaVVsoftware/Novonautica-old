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

        return $this->render(':estructura:sidebar.html.twig', [
            'menus' => $this->paths,
            'current_path' => $this->get('request_stack')->getMasterRequest()->getRequestUri()
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
                'icon' => 'ship',
                'path' => $this->generateUrl('marina-administracion'),
                'submenu' => [
                    ['name' => 'Administracion', 'path' => $this->generateUrl('marina-administracion')],
                    [
                        'name' => 'Cotizaciones',
                        'path' => $this->generateUrl('marina-humeda_index'),
                        'submenu' => [
                            ['name' => 'Listado', 'path' => $this->generateUrl('marina-humeda_index')],
                            ['name' => 'Nuevo', 'path' => $this->generateUrl('marina-humeda_new')],
                            ['name' => 'Tarifas', 'path' => $this->generateUrl('marinahumeda-tarifas_index')],
                        ]
                    ]
                ]
            ],

        ]);
    }

    private function admin()
    {
        $this->add([
        ]);
    }

    private function add(Array $paths)
    {
        return $this->paths = array_merge($this->paths, $paths);
    }
}
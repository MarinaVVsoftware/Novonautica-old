<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/inicio", name="incio")
     */
    public function displayAdminIndex(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('inicio.twig', [
        ]);
    }

//    /**
//     * @Route("/astillero/nueva-cotizacion", name="astillero-nueva-cotizacion")
//     */
//    public function displayAstilleroNuevaCotizacion(Request $request)
//    {
//        return $this->render('astillero-nueva-cotizacion.twig', [
//            'astilleronuevacotizacion' => 1
//        ]);
//    }
    /**
     * @Route("/astillero/cotizaciones", name="astillero-cotizaciones")
     */
    public function displayAstilleroCotizaciones(Request $request)
    {
        return $this->render('astillero-cotizaciones.twig', [
            'astillerocotizaciones' => 1
        ]);
    }
    /**
     * @Route("/astill/aceptaciones", name="astillero-aceptaciones")
     */
    public function displayAstilleroAceptaciones(Request $request)
    {
        return $this->render('astillero-aceptaciones.twig', [
            'astilleroaceptaciones' => 1
        ]);
    }
    /**
     * @Route("/astill/odt", name="astillero-odt")
     */
    public function displayAstilleroODT(Request $request)
    {
        return $this->render('astillero-odt.twig', [
            'astilleroodt' => 1
        ]);
    }
    /**
     * @Route("/astillero/odt/asigna-dias", name="astillero-odt-dias")
     */
    public function displayAstilleroODTDias(Request $request)
    {
        return $this->render('astillero-odt-dias.twig', [
            'astilleroodt' => 1
        ]);
    }
    /**
     * @Route("/astillero/odt/asigna-horas", name="astillero-odt-horas")
     */
    public function displayAstilleroODTHoras(Request $request)
    {
        return $this->render('astillero-odt-horas.twig', [
            'astilleroodt' => 1
        ]);
    }

    /**
     * @Route("/recursos-humanos", name="recursos-humanos")
     */
    public function displayRecursosHumanos(Request $request)
    {
        return $this->render('recursos-humanos.twig', [
            'recursoshumanos' => 1
        ]);
    }
    /**
     * @Route("/contabilidad", name="contabilidad")
     */
    public function displayContabilidad(Request $request)
    {
        return $this->render('contabilidad.twig', [
            'contabilidad' => 1
        ]);
    }
    /**
     * @Route("/reportes", name="reportes")
     */
    public function displayReportes(Request $request)
    {
        return $this->render('reportes.twig', [
            'reportes' => 1
        ]);
    }
}

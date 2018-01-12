<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pais;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\MarinaHumedaCotizacion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="inicio")
     */
    public function displayAdminIndex(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('inicio.twig', [
        ]);
    }

    /**
     * @Route("/marina/cotizacion", name="marina-humeda_index")
     */
    public function displayCotizacionIndex(Request $request)
    {

    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/mhc-pdf", name="marinahc-pdf")
     * @param MarinaHumedaCotizacion $mhc
     *
     * @return Response
     */
    public function displayMarinaPDF(MarinaHumedaCotizacion $mhc)
    {
        return $this->render('marinahumeda/cotizacion/cotizacionpdf.html.twig', [
            'marinaHumedaCotizacion' => $mhc
        ]);
    }

    /**
     * Displays a form to edit an existing marinaHumedaCotizacion entity.
     *
     * @Route("/{id}/correovalidacion", name="marina-humeda_validaras")
     **/
    public function validaAction(Request $request, MarinaHumedaCotizacion $mhc)
    {
        return $this->render('marinahumeda/cotizacion/correo-clientevalida.twig', [
            'marinaHumedaCotizacion' => $mhc,
            'tokenAcepta' => 'asdas',
            'tokenRechaza' => 'otro'
        ]);
    }

//    /**
//     * @Route("/astillero/aceptaciones", name="astillero-aceptaciones")
//     */
//    public function displayAstilleroAceptaciones()
//    {
//        return $this->render('astillero-aceptaciones.twig', [
//            'title' => 'Aceptaciones'
//        ]);
//    }

//    /**
//     * @Route("/astillero/odt", name="astillero-odt")
//     */
//    public function displayAstilleroODT(Request $request)
//    {
//        return $this->render('astillero-odt.twig');
//    }

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

    /**
     * Retorna todos los estados en base a un id de un pais
     *
     * @Route("/pais/{pais}/estados.{_format}", name="estados_index", defaults={"_format" = "json"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $pais
     *
     * @return Response
     */
    public function estadosAction(Request $request, Pais $pais)
    {
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(['country']);
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);

        return new Response($serializer->serialize($pais, $request->getRequestFormat()));
    }
}

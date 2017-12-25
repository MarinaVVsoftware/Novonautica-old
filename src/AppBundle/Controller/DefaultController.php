<?php

namespace AppBundle\Controller;

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
     * @Route("/contabilidad/facturacion/", name="display_cotizacion_facturacion")
     */
    public function displayCotizacionFacturaIndex()
    {
        return $this->render('index-facturacion.html.twig', ['title' => 'Facturas']);
    }

    /**
     * @Route("/contabilidad/facturacion/new", name="display_new_cotizacion_facturacion")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayCotizacionFacturaNew(Request $request)
    {
        $form = $this
            ->createFormBuilder([])
            ->add('cotizacion', EntityType::class, [
                'label' => 'Seleccionar una cotización',
                'class' => 'AppBundle\Entity\MarinaHumedaCotizacion'
            ])
            ->add('rfc', TextType::class, ['label' => 'RFC'])
            ->add('cliente', TextType::class)
            ->add('empresa', TextType::class, ['label' => 'Nombre de la empresa'])
            ->add('pais', ChoiceType::class, [
                'label' => 'País',
                'choices' => ['México']
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => ['Quintana Roo']
            ])
            ->add('ciudad', TextType::class)
            ->add('direccionFiscal', TextareaType::class)
            ->add('codigoPostal', TextType::class)
            ->add('numeroTelefonico', TextType::class)
            ->add('correoElectronico', TextType::class)
            ->add('fechaExpedicion', TextType::class)
            ->add('lugarExpedicion', TextType::class)
            ->add('tipoCambio', TextType::class)
            ->add('condicionesPago', TextType::class)
            ->add('cuenta', TextType::class)
            ->add('referenciaPago', TextType::class)
            ->add('iva', TextType::class, ['label' => 'IVA'])
            ->getForm();


        return $this->render('new-facturacion.html.twig', [
            'title' => 'Nueva factura',
            'form' => $form->createView()
        ]);
    }

    /**
     * Genera el pdf de una cotizacion en base a su id
     *
     * @Route("/{id}/mhc-pdf", name="marinahc-pdf")
     * @param MarinaHumedaCotizacion $mhc
     *
     * @return \Symfony\Component\HttpFoundation\Response
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

    /**
     * @Route("/astillero/aceptaciones", name="astillero-aceptaciones")
     */
    public function displayAstilleroAceptaciones()
    {
        return $this->render('astillero-aceptaciones.twig', [
            'title' => 'Aceptaciones'
        ]);
    }

    /**
     * @Route("/astillero/odt", name="astillero-odt")
     */
    public function displayAstilleroODT(Request $request)
    {
        return $this->render('astillero-odt.twig');
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

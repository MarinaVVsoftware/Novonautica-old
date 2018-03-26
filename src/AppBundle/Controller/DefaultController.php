<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\CuentaBancaria;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\AstilleroCotizacionAceptadaType;
use AppBundle\Form\AstilleroCotizacionRechazadaType;
use AppBundle\Form\MarinaHumedaCotizacionAceptadaType;
use AppBundle\Form\MarinaHumedaCotizacionRechazadaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\MarinaHumedaCotizacion;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/gracias", name="marina-humeda_gracias")
     * @Method("GET")
     */
    public function graciasMarinaAction()
    {
        return $this->render('marinahumeda/cotizacion/gracias.twig', [
        ]);
    }

    /**
     * Confirma la respuesta de un cliente a una cotizacion de marina
     *
     * @Route("/{token}/confirma", name="respuesta-cliente")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $token
     *
     * @return Response
     */
    public function repuestaMarinaClienteAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $marinacotizacionAceptar = $em->getRepository(MarinaHumedaCotizacion::class)
            ->findOneBy(['tokenacepta' => $token]);
        $astillerocotizacionAceptar = $em->getRepository(AstilleroCotizacion::class)
            ->findOneBy(['tokenacepta' => $token]);
        $marinacotizacionRechazar = $em->getRepository(MarinaHumedaCotizacion::class)
            ->findOneBy(['tokenrechaza' => $token]);
        $astillerocotizacionRechazar = $em->getRepository(AstilleroCotizacion::class)
            ->findOneBy(['tokenrechaza' => $token]);

        if ($marinacotizacionAceptar) {
            $cotizacionAceptar = $marinacotizacionAceptar;
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)->findAll();

            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema = $query->getArrayResult();
            $diasHabiles = $sistema[0]['diasHabilesMarinaCotizacion'];

            $folio = $cotizacionAceptar->getFoliorecotiza() == 0
                ? $cotizacionAceptar->getFolio()
                : $cotizacionAceptar->getFolio() . '-' . $cotizacionAceptar->getFoliorecotiza();

            $valorSistema = new ValorSistema();
            $codigoSeguimiento = $folio . '-' . $valorSistema->generaToken(10);
            $cotizacionAceptar
                ->setValidacliente(2)
                ->setCodigoseguimiento($codigoSeguimiento);

            // Fecha en la que acepto el cliente
            $cotizacionAceptar->setRegistroValidaCliente(new \DateTimeImmutable());

            $em->persist($cotizacionAceptar);
            $em->flush();

            $mensaje1 = '¡Enhorabuena!';
            $mensaje2 = 'La cotización ' . $folio . ' ha sido aprobada.';
            $suformulario = 1;

            $editForm = $this->createForm(MarinaHumedaCotizacionAceptadaType::class, $cotizacionAceptar);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $cotizacionAceptar->setFecharespuesta(new \DateTime('now'));
                $em->persist($cotizacionAceptar);
                $em->flush();
                return $this->redirectToRoute('marina-humeda_gracias');
            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'suformulario' => $suformulario,
                'cuentaBancaria' => $cuentaBancaria,
                'diasHabiles' => $diasHabiles,
                'form' => $editForm->createView(),
                'marinaHumedaCotizacion' => $cotizacionAceptar
            ]);
        }

        if ($astillerocotizacionAceptar) {
            $cotizacionAceptar = $astillerocotizacionAceptar;
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)->findAll();

            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema = $query->getArrayResult();
            $diasHabiles = $sistema[0]['diasHabilesAstilleroCotizacion'];

            $folio = $cotizacionAceptar->getFoliorecotiza() == 0
                ? $cotizacionAceptar->getFolio()
                : $cotizacionAceptar->getFolio() . '-' . $cotizacionAceptar->getFoliorecotiza();

            $valorSistema = new ValorSistema();
            $codigoSeguimiento = $folio . '-' . $valorSistema->generaToken(10);

            $cotizacionAceptar->setValidacliente(2);
            $cotizacionAceptar->setCodigoseguimiento($codigoSeguimiento);

            $em->persist($cotizacionAceptar);
            $em->flush();

            $mensaje1 = '¡Enhorabuena!';
            $mensaje2 = 'La cotización ' . $folio . ' ha sido aprobada.';
            $suformulario = 1;

            $editForm = $this->createForm(AstilleroCotizacionAceptadaType::class, $cotizacionAceptar);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $cotizacionAceptar->setFecharespuesta(new \DateTime('now'));
                $em->persist($cotizacionAceptar);
                $em->flush();
                return $this->redirectToRoute('astillero_gracias');
            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'suformulario' => $suformulario,
                'cuentaBancaria' => $cuentaBancaria,
                'diasHabiles' => $diasHabiles,
                'form' => $editForm->createView(),
                'marinaHumedaCotizacion' => $cotizacionAceptar
            ]);
        }

        if ($astillerocotizacionRechazar) {
            $cotizacionRechazar = $astillerocotizacionRechazar;
            $cotizacionRechazar->setValidacliente(1);

            $em->persist($cotizacionRechazar);
            $em->flush();

            $folio = $cotizacionRechazar->getFoliorecotiza() == 0
                ? $cotizacionRechazar->getFolio()
                : $cotizacionRechazar->getFolio() . '-' . $cotizacionRechazar->getFoliorecotiza();

            $mensaje1 = '¡Oh-oh!';
            $mensaje2 = 'La cotización ' . $folio . ' no ha sido aprobada.';
            $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
            $suformulario = 2;

            $editForm = $this->createForm(AstilleroCotizacionRechazadaType::class, $cotizacionRechazar);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em->flush();
                return $this->redirectToRoute('astillero_gracias');
            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'mensaje3' => $mensaje3,
                'suformulario' => $suformulario,
                'form' => $editForm->createView()
            ]);
        }

        if ($marinacotizacionRechazar) {
            $cotizacionRechazar = $marinacotizacionRechazar;
            $cotizacionRechazar->setValidacliente(1);
            $cotizacionRechazar->setRegistroValidaCliente(new \DateTimeImmutable());

            $em->persist($cotizacionRechazar);
            $em->flush();

            $folio = $cotizacionRechazar->getFoliorecotiza() == 0
                ? $cotizacionRechazar->getFolio()
                : $cotizacionRechazar->getFolio() . '-' . $cotizacionRechazar->getFoliorecotiza();

            $mensaje1 = '¡Oh-oh!';
            $mensaje2 = 'La cotización ' . $folio . ' no ha sido aprobada.';
            $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
            $suformulario = 2;

            $editForm = $this->createForm(MarinaHumedaCotizacionRechazadaType::class, $cotizacionRechazar);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em->flush();
                return $this->redirectToRoute('marina-humeda_gracias');
            }

            return $this->render('marinahumeda/cotizacion/respuesta-cliente.twig', [
                'mensaje1' => $mensaje1,
                'mensaje2' => $mensaje2,
                'mensaje3' => $mensaje3,
                'suformulario' => $suformulario,
                'form' => $editForm->createView()
            ]);
        }
    }
}

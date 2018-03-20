<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/20/18
 * Time: 12:49
 */

namespace AppBundle\Controller;


use AppBundle\Entity\AstilleroCotizacion;
use AppBundle\Entity\CuentaBancaria;
use AppBundle\Entity\MarinaHumedaCotizacion;
use AppBundle\Entity\ValorSistema;
use AppBundle\Form\AstilleroCotizacionAceptadaType;
use AppBundle\Form\AstilleroCotizacionRechazadaType;
use AppBundle\Form\MarinaHumedaCotizacionAceptadaType;
use AppBundle\Form\MarinaHumedaCotizacionRechazadaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Controlador para confirmaciones, eventos, etc. para permitir mostrar contenido sin acceso
 *
 * @Route("/")
 */
class AnonController extends Controller
{
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
     * @Route("/gracias", name="astillero_gracias")
     * @Method("GET")
     */
    public function graciasAstilleroAction()
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
        $cotizacionAceptar = $em->getRepository(MarinaHumedaCotizacion::class)
            ->findOneBy(['tokenacepta' => $token]);

        if ($cotizacionAceptar) {
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)
                ->findAll();
            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema = $query->getArrayResult();

            $diasHabiles = $sistema[0]['diasHabilesMarinaCotizacion'];

            if ($cotizacionAceptar->getFoliorecotiza() == 0) {
                $folio = $cotizacionAceptar->getFolio();
            } else {
                $folio = $cotizacionAceptar->getFolio() . '-' . $cotizacionAceptar->getFoliorecotiza();
            }

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
        else {
            $cotizacionRechazar = $em->getRepository(MarinaHumedaCotizacion::class)
                ->findOneBy(['tokenrechaza' => $token]);

            if ($cotizacionRechazar) {
                $cotizacionRechazar->setValidacliente(1);
                $cotizacionRechazar->setRegistroValidaCliente(new \DateTimeImmutable());
                $em->persist($cotizacionRechazar);
                $em->flush();

                if ($cotizacionRechazar->getFoliorecotiza() == 0) {
                    $folio = $cotizacionRechazar->getFolio();
                } else {
                    $folio = $cotizacionRechazar->getFolio() . '-' . $cotizacionRechazar->getFoliorecotiza();
                }

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

    /**
     * Confirma la respuesta de un cliente a una cotizacion
     *
     * @Route("/{token}/confirma", name="respuesta-cliente-astillero")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $token
     *
     * @return Response
     */
    public function repuestaAstilleroClienteAction(Request $request, $token)
    {

        $em = $this->getDoctrine()->getManager();
        $cotizacionAceptar = $em->getRepository(AstilleroCotizacion::class)
            ->findOneBy(['tokenacepta'=>$token]);

        if($cotizacionAceptar) {
            $cuentaBancaria = $em->getRepository(CuentaBancaria::class)
                ->findAll();
            $qb = $em->createQueryBuilder();
            $query = $qb->select('v')->from(valorSistema::class, 'v')->getQuery();
            $sistema =$query->getArrayResult();

            $diasHabiles = $sistema[0]['diasHabilesAstilleroCotizacion'];

            if($cotizacionAceptar->getFoliorecotiza()==0){
                $folio = $cotizacionAceptar->getFolio();
            }else{
                $folio = $cotizacionAceptar->getFolio().'-'.$cotizacionAceptar->getFoliorecotiza();
            }
            $valorSistema = new ValorSistema();
            $codigoSeguimiento = $folio.'-'.$valorSistema->generaToken(10);

            $cotizacionAceptar->setValidacliente(2);
            $cotizacionAceptar->setCodigoseguimiento($codigoSeguimiento);
            $em->persist($cotizacionAceptar);
            $em->flush();

            $mensaje1 = '¡Enhorabuena!';
            $mensaje2 = 'La cotización '.$folio.' ha sido aprobada.';
            $suformulario = 1;

            $editForm = $this->createForm(AstilleroCotizacionAceptadaType::class, $cotizacionAceptar);
            $editForm ->handleRequest($request);
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $cotizacionAceptar->setFecharespuesta (new \DateTime('now'));
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
        else{
            $cotizacionRechazar = $em->getRepository(AstilleroCotizacion::class)
                ->findOneBy(['tokenrechaza'=>$token]);
            if($cotizacionRechazar){
                $cotizacionRechazar->setValidacliente(1);
                $em->persist($cotizacionRechazar);
                $em->flush();
                if($cotizacionRechazar->getFoliorecotiza()==0){
                    $folio = $cotizacionRechazar->getFolio();
                }else{
                    $folio = $cotizacionRechazar->getFolio().'-'.$cotizacionRechazar->getFoliorecotiza();
                }
                $mensaje1 = '¡Oh-oh!';
                $mensaje2 = 'La cotización '.$folio.' no ha sido aprobada.';
                $mensaje3 = 'Nos gustaría saber su opinión o comentarios del motivo de su rechazo.';
                $suformulario = 2;

                $editForm = $this->createForm(AstilleroCotizacionRechazadaType::class, $cotizacionRechazar);
                $editForm ->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $em->flush();
                    return $this->redirectToRoute('astillero_gracias');
                }

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
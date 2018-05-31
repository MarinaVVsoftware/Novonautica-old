<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 28/05/2018
 * Time: 05:22 PM
 */

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\OrdenDeTrabajo;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 *
 * @Route("contabilidad/odt")
 */
class ODTController extends Controller
{
    /**
     * Lists all ordenDeTrabajo entities.
     *
     * @Route("/", name="contabilidad_odt_index")
     * @Method("GET")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $results = $dataTables->handle($request, 'ODT');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getStatusCode());
            }
        }

        return $this->render('ordendetrabajo/index-pagos.twig', [
            'title' => 'Ordenes de trabajo'
        ]);
    }

    /**
     * @Route("/{id}", name="contabilidad_odt_show")
     * @Method("GET")
     *
     * @param OrdenDeTrabajo $ordenDeTrabajo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(OrdenDeTrabajo $ordenDeTrabajo)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_PAGO', $ordenDeTrabajo);

        return $this->render('ordendetrabajo/hoja-pagos.html.twig', [
            'title' => 'Detalle ODT',
            'ordenDeTrabajo' => $ordenDeTrabajo
        ]);
    }

    /**
     * @Route("/{id}/contratista-pago", name="ordendetrabajo_contratista_pago")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Contratista $contratista
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function pagoAction(Request $request, Contratista $contratista)
    {
        $this->denyAccessUnlessGranted('ROLE_ODT_PAGO', $contratista);
        $pagadoMXN =0;
        $dolar = $contratista->getAstilleroODT()->getAstilleroCotizacion()->getDolar();
        $originalPagos = new ArrayCollection();
        foreach ($contratista->getContratistapagos() as $pago) {
            $originalPagos->add($pago);
            if($pago->getDivisa()=='USD'){
                $pagadoMXN+=($pago->getCantidad()*$dolar)/100;
            }else{
                $pagadoMXN+=$pago->getCantidad();
            }
        }
        $saldoMXN = $contratista->getTotal() - $pagadoMXN;
        $editForm = $this->createForm('AppBundle\Form\Astillero\ContratistaPagoType', $contratista);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($originalPagos as $pago) {
                if (false === $contratista->getContratistapagos()->contains($pago)) {
                    $pago->getContratista()->removeContratistapago($pago);
                    $em->persist($pago);
                    $em->remove($pago);
                }
            }
            $cantidadPago = 0;
            $saldo = 0;
            foreach ($contratista->getContratistapagos() as $unpago){
                if($unpago->getDivisa()=='USD'){
                    $cantidadPago+=($unpago->getCantidad()*$dolar)/100;
                }else{
                    $cantidadPago+=$unpago->getCantidad();
                }
                $saldo = $contratista->getTotal() - $cantidadPago;
                $unpago->setSaldo($saldo);
            }
            if ($saldo < -1) {
                $this->addFlash(
                    'notice',
                    'Error! Se ha intentado pagar más que el saldo restante'
                );
            } else {
                $em->persist($contratista);
                $em->flush();
                return $this->redirectToRoute('contabilidad_odt_show', ['id' => $contratista->getAstilleroODT()->getId()]);
            }

        }

        return $this->render('ordendetrabajo/pago.html.twig', [
            'title' => 'Registrar Pago Contratista',
            'contratista' => $contratista,
            'edit_form' => $editForm->createView(),
            'pagadoMXN' => $pagadoMXN,
            'saldoMXN' => $saldoMXN
        ]);
    }
}
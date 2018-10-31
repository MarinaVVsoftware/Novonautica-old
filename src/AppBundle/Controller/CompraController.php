<?php
/**
 * Created by PhpStorm.
 * User: Holograma
 * Date: 02/11/2018
 * Time: 08:00 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Compra;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * @Route("compras")
 */
class CompraController extends Controller
{
    /**
     * @Route("/", name="compra_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
        if($request->isXmlHttpRequest()){
            try{
                $results = $dataTables->handle($request,'compra');
                return $this->json($results);
            } catch(HttpException $e){
                return $this->json($e->getMessage(),$e->getStatusCode());
            }
        }
        return $this->render('compra/index.html.twig',['title' => 'Compras']);
    }

    /**
     * @Route("/nuevo", name="compra_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $compra = new Compra();
        $this->denyAccessUnlessGranted('COMPRA_CREATE',$compra);
        $em = $this->getDoctrine()->getManager();
        $valor = $em->getRepository('AppBundle:ValorSistema')->findAll()[0];
        $compra->setIva($valor->getIva());
        $form = $this->createForm('AppBundle\Form\CompraType',$compra);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $variable = $em->getRepository('AppBundle:ValorSistema')->findAll()[0];
            $folioNuevo = $variable->getFolioCompra() + 1;
            $compra->setFolio($folioNuevo);
            $compra->setFecha(new \DateTime('now'));
            $compra->setCreador($this->getUser());
            $variable->setFolioCompra($folioNuevo);
            $em->persist($compra);
            $em->persist($variable);
            $em->flush();
            return $this->redirectToRoute('compra_show',['id' => $compra->getId()]);
        }
        return $this->render('compra/edit.html.twig',[
            'form' => $form->createView(),
            'title' => 'Nueva compra'
        ]);
    }

    /**
     * @Route("/{id}", name="compra_show")
     * @Method("GET")
     * @param Compra $compra
     * @return Response
     */
    public function showAction(Compra $compra)
    {
        return $this->render('compra/show.html.twig',[
            'title' => 'Detalle compra',
            'compra' => $compra
        ]);
    }

    /**
     * @Route("/{id}/editar", name="compra_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Compra $compra)
    {
        $this->denyAccessUnlessGranted('COMPRA_EDIT',$compra);

        $em = $this->getDoctrine()->getManager();

        if($compra->getValidado()){ throw new NotFoundHttpException(); }

        $compra = $em->getRepository(Compra::class)->find($compra->getId());
        $originalConceptos = new ArrayCollection();
        foreach ($compra->getConceptos() as $concepto){
            $originalConceptos->add($concepto);
        }
        $deleteForm = $this->createDeleteForm($compra);
        $editForm = $this->createForm('AppBundle\Form\CompraType',$compra);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            foreach ($originalConceptos as $concepto){
                if(false === $compra->getConceptos()->contains($concepto)){
                    $concepto->getCompra()->removeConcepto($concepto);
                    $em->persist($concepto);
                    $em->remove($concepto);
                }
            }
            if($compra->getValidado()){
                $compra->setNombreValido($this->getUser()->getNombre());
            }
            $em->persist($compra);
            $em->flush();
            return $this->redirectToRoute('compra_show',['id' => $compra->getId()]);
        }

        return $this->render('compra/edit.html.twig',[
            'form' => $editForm->createView(),
            'compra' => $compra,
            'delete_form' => $deleteForm->createView(),
            'title' => 'Editar compra'
        ]);
    }

    /**
     * @Route("/{id}/solicitud.json", name="solicitud_json")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getSolicitudAction(Request $request,$id)
    {
        $solicitudConceptos = $this->getDoctrine()->getRepository('AppBundle:Solicitud\Concepto')->getConceptos($id);
        return $this->json(
            $solicitudConceptos,
            JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="compra_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Compra $compra
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Compra $compra)
    {
        $this->denyAccessUnlessGranted('COMPRA_DELETE',$compra);
        $form = $this->createDeleteForm($compra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($compra);
            $em->flush();
        }

        return $this->redirectToRoute('compra_index');
    }

    /**
     * @param Compra $compra
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Compra $compra)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('compra_delete', ['id' => $compra->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
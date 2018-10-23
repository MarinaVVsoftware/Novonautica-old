<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 08/10/2018
 * Time: 11:33 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Gasto;
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
 * Class GastoController
 * @Route("gastos")
 */
class GastoController extends Controller
{
    /**
     * @Route("/", name="gasto_index")
     * @Method("GET")
     * @param Request $request
     * @param DataTablesInterface $dataTables
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request, DataTablesInterface $dataTables)
    {
       if($request->isXmlHttpRequest()){
           try{
               $results = $dataTables->handle($request,'gasto');
               return $this->json($results);
           } catch(HttpException $e){
               return $this->json($e->getMessage(),$e->getStatusCode());
           }
       }
        return $this->render('gasto/index.html.twig',['title' => 'Gastos']);
    }

    /**
     * @Route("/nuevo", name="gasto_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $gasto = new Gasto();
        $this->denyAccessUnlessGranted('GASTO_CREATE',$gasto);
        $form = $this->createForm('AppBundle\Form\GastoType',$gasto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $gasto->setFecha(new \DateTime('now'));
            $em->persist($gasto);
            $em->flush();

            return $this->redirectToRoute('gasto_index');
        }

        return $this->render('gasto/edit.html.twig',[
            'form' => $form->createView(),
            'title' => 'Nuevo gasto'
        ]);
    }

    /**
     * @Route("/{id}", name="gasto_show")
     * @Method("GET")
     * @param Gasto $gasto
     * @return Response
     */
    public function showAction(Gasto $gasto)
    {
        $em = $this->getDoctrine()->getManager();
        $permiso = $em->getRepository(Gasto::class)
                      ->compruebaRol($this->getUser()->getRoles(),$gasto->getEmpresa()->getId());
        if(!$permiso){ throw new NotFoundHttpException(); }
        return $this->render('gasto/show.html.twig',[
            'title' => 'Detalle gasto',
            'gasto' => $gasto
        ]);
    }

    /**
     * @Route("/{id}/editar", name="gasto_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Gasto $gasto)
    {
        $this->denyAccessUnlessGranted('GASTO_EDIT',$gasto);

        $em = $this->getDoctrine()->getManager();
        $permiso = $em->getRepository(Gasto::class)
            ->compruebaRol($this->getUser()->getRoles(),$gasto->getEmpresa()->getId());
        if(!$permiso){ throw new NotFoundHttpException(); }

        $gasto = $em->getRepository(Gasto::class)->find($gasto->getId());
        $originalConceptos = new ArrayCollection();
        foreach ($gasto->getConceptos() as $concepto){
            $originalConceptos->add($concepto);
        }
        $deleteForm = $this->createDeleteForm($gasto);
        $editForm = $this->createForm('AppBundle\Form\GastoType',$gasto);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            foreach ($originalConceptos as $concepto){
                if(false === $gasto->getConceptos()->contains($concepto)){
                    $concepto->getGasto()->removeConcepto($concepto);
                    $em->persist($concepto);
                    $em->remove($concepto);
                }
            }
            $em->persist($gasto);
            $em->flush();
            return $this->redirectToRoute('gasto_index');
        }

        return $this->render('gasto/edit.html.twig',[
           'form' => $editForm->createView(),
           'delete_form' => $deleteForm->createView(),
           'title' => 'Editar gasto'
        ]);
    }

    /**
     * @Route("/{id}", name="gasto_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Gasto $gasto
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Gasto $gasto)
    {
        $this->denyAccessUnlessGranted('GASTO_DELETE',$gasto);
        $form = $this->createDeleteForm($gasto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gasto);
            $em->flush();
        }

        return $this->redirectToRoute('gasto_index');
    }

    /**
     * @param Gasto $gasto
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Gasto $gasto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gasto_delete', ['id' => $gasto->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
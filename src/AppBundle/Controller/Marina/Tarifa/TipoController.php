<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-02-06
 * Time: 11:42
 */

namespace AppBundle\Controller\Marina\Tarifa;


use AppBundle\Form\Marina\Tarifa\TiposType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/marina/cotizacion/estadia/tarifas/tipo")
 *
 * Class TipoController
 * @package AppBundle\Controller\Marina\Tarifa
 */
class TipoController extends AbstractController
{
    /**
     * @Route("/", name="marinahumeda-tarifas-tipos-index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $tipos = [];

        $form = $this->createForm(TiposType::class, $tipos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $savedTipos = $form->getData()['tipos'];

            foreach ($savedTipos as $savedTipo) {
                $em->persist($savedTipo);
            }

            $em->flush();
            return $this->redirectToRoute('marinahumeda-tarifas-tipos-index');
        }

        return $this->render(
            ':marinahumeda/tarifa/tipo:index.html.twig',
            [
                'title' => 'Tipos',
                'form' => $form->createView(),
            ]
        );
    }
}

<?php

namespace AppBundle\Controller\Contabilidad\Egreso;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/reporte/egreso")
 */
class ReporteController extends AbstractController
{
    /**
     * @Route("/", name="reporte_egreso")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $gastos = [];
        $form = $this->createFormBuilder()
            ->add('inicio', DateType::class, [
                'label' => 'Fecha inicio',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
            ])
            ->add('fin', DateType::class, [
                'label' => 'Fecha fin',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario', 'readonly' => true],
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
            ])
            ->add('concepto',EntityType::class,[
                'class' => 'AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto',
                'placeholder' => 'Todos',
                'required' => false,
                'attr' => ['class' => 'select-buscador' ]
            ])
            ->add('tipo',EntityType::class,[
                'class' => 'AppBundle\Entity\Contabilidad\Egreso\Tipo',
                'placeholder' => 'Todos',
                'required' => false,
                'attr' => ['class' => 'select-buscador' ]
            ])
            ->add('empresa',EntityType::class,[
                'class' => 'AppBundle\Entity\Contabilidad\Facturacion\Emisor',
                'placeholder' => 'Todos',
                'required' => false,
                'attr' => ['class' => 'select-buscador' ]
            ])
            ->add('buscar', SubmitType::class, [
                'attr' => ['class' => 'btn-xs btn-azul pull-right no-loading'],
                'label' => 'Buscar'
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $datos = $form->getData();
            $idconcepto = $datos['concepto'] ? $datos['concepto']->getId() : 0;
            $idempresa = $datos['empresa'] ? $datos['empresa']->getId() : 0;
            $idtipo = $datos['tipo'] ? $datos['tipo']->getId() : 0;

            $em = $this->getDoctrine()->getManager();
            $gastos = $em->getRepository('AppBundle:Contabilidad\Egreso\Entrada')
                ->getReporteEgresos($idempresa,
                    $idconcepto,
                    $idtipo,
                    $datos['inicio']->format('Y-m-d'),
                    $datos['fin']->format('Y-m-d'));
        }
        return $this->render('contabilidad/egreso/reporte.html.twig',[
            'gastos' => $gastos,
            'title' => 'Reporte Egresos',
            'form' => $form->createView()
        ]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 25/09/2018
 * Time: 03:07 PM
 */

namespace AppBundle\Controller\Tienda;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


/**
 * @package AppBundle\Controller\Tienda
 * @Route("/reporte/tienda")
 */
class ReporteController extends AbstractController
{
    /**
     * Muestra los adeudos y abonos sumados de los clientes que han cotizado en astillero
     *
     * @Route("/", name="reporte_store_venta")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexReporteAction(Request $request)
    {
        $ventas = [];
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
            ->add('producto',EntityType::class,[
                'class' => 'AppBundle\Entity\Tienda\Producto',
                'placeholder' => 'Todos',
                'required' => false
            ])
            ->add('buscar', SubmitType::class, [
                'attr' => ['class' => 'btn-xs btn-azul pull-right no-loading'],
                'label' => 'Buscar'
            ])
            ->add('xls', SubmitType::class, [
                'attr' => ['class' => 'btn-xs btn-verde no-loading'],
                'label' => 'xls'
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $datos = $form->getData();
            $idproducto = $datos['producto'] ? $datos['producto']->getId() : '0';
            if($form->get('buscar')->isCLicked()){
                $em = $this->getDoctrine()->getManager();
                $ventas = $em->getRepository('AppBundle:Tienda\Venta\Concepto')
                             ->getReporteVentas($idproducto,
                                                $datos['inicio']->format('Y-m-d'),
                                                $datos['fin']->format('Y-m-d')
                             );
            }else{
                return $this->redirectToRoute('reporte_store_venta_data', [
                    'idproducto' => $idproducto,
                    'inicio' => $datos['inicio']->format('Y-m-d'),
                    'fin' => $datos['fin']->format('Y-m-d')
                ]);
            }
        }
        return $this->render('tienda/reporte/venta.html.twig',[
            'ventas' => $ventas,
            'title' => 'Reporte tienda',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/venta/{idproducto}/{inicio}/{fin}", name="reporte_store_venta_data")
     * @Method({"GET", "POST"})
     * @param $idproducto
     * @param $inicio
     * @param $fin
     * @return StreamedResponse
     */
    public function generateCsvAction($idproducto,$inicio,$fin)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use($idproducto,$inicio,$fin) {
            $handle = fopen('php://output', 'w+');
            $auxTitulos = [
                'Fecha venta',
                'Producto',
                'Cantidad',
                'Precio',
                'Sub-Total',
                'IVA',
                'Descuento',
                'Total'
            ];
            $titulos = [];
            foreach ($auxTitulos as $auxTitulo) {
                array_push($titulos, mb_convert_encoding($auxTitulo, 'UTF-16LE', 'UTF-8'));
            }
            fputcsv($handle, $titulos);
            $em = $this->getDoctrine()->getManager();
            $ventas = $em->getRepository('AppBundle:Tienda\Venta\Concepto')
                         ->getReporteVentas($idproducto,$inicio,$fin);
            foreach ($ventas as $dato) {
                fputcsv($handle, [
                        $dato['fecha'],
                        $dato['producto'],
                        $dato['cantidad'],
                        $dato['precio'],
                        $dato['subtotal'],
                        $dato['iva'],
                        $dato['descuento'],
                        $dato['total'],
                    ]);
            }
            fclose($handle);
        });
        $response->setStatusCode(200);
        $response->setCharset('UTF-8');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="TiendaEjemplo.csv"');
        return $response;
    }
}
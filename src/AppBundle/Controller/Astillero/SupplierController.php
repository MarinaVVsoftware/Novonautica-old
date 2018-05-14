<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 5/14/18
 * Time: 12:58
 */

namespace AppBundle\Controller\Astillero;


use AppBundle\Entity\Barco;
use AppBundle\Entity\OrdenDeTrabajo;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SupplierController
 * @package AppBundle\Controller\Astillero
 *
 * @Route("suppliers")
 */
class SupplierController extends AbstractController
{
    /**
     * @Route("/", name="suppliers_index")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->generateTrabajosForm()->getForm();
        $form->handleRequest($request);


        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Astillero\Contratista');

        $trabajos = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $odt = $form->getData()['ordenDeTrabajo'];

            $trabajos = $repository->findBy([
                'astilleroODT' => $odt->getId(),
                'proveedor' => $this->getUser()->getId(),
            ]);
        }

        return $this->render('suppliers/index.html.twig', [
            'title' => 'Bienvenido',
            'form' => $form->createView(),
            'trabajos' => $trabajos
        ]);
    }

    /**
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function generateTrabajosForm()
    {
        return $this->createFormBuilder([])
            ->add('ordenDeTrabajo', EntityType::class, [
                'class' => OrdenDeTrabajo::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('odt')
                        ->select('odt', 'cotizacion')
                        ->leftJoin('odt.astilleroCotizacion', 'cotizacion')
                        ->leftJoin('odt.contratistas', 'contratistas')
                        ->where('IDENTITY(contratistas.proveedor) = :proveedor')
                        ->setParameter('proveedor', $this->getUser()->getId())
                        ->orderBy('cotizacion.folio', 'DESC')
                        ;
                },
                'choice_label' => function ($odt) {
                    /** @var OrdenDeTrabajo $odt */
                    $cotizacion = $odt->getAstilleroCotizacion();
                    return 'Folio: ' . $cotizacion->getFolioString();
                }
            ]);
    }
}
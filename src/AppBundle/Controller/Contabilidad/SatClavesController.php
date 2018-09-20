<?php
/**
 * User: inrumi
 * Date: 9/20/18
 * Time: 15:07
 */

namespace AppBundle\Controller\Contabilidad;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Facturacion controller.
 *
 * @Route("contabilidad/claves-sat")
 */
class SatClavesController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/select2/claveprodserv")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function claveProdServAction(Request $request)
    {
        $query = $request->query->get('q');

        $cps = $this->entityManager
            ->getRepository(ClaveProdServ::class)
            ->findAllLikeSelect2($query);

        return new JsonResponse(
            [
                'results' => $cps,
            ]
        );
    }

    /**
     * @Route("/select2/claveunidad")
     *
     * @param Request $request
     *
     * @return string
     */
    public function getAllClavesUnidad(Request $request)
    {
        $q = $request->query->get('q');

        $clavesUnidad = $this->entityManager
            ->getRepository(ClaveUnidad::class)
            ->findAllLikeSelect2($q);

        return new JsonResponse(
            [
                'results' => $clavesUnidad,
            ]
        );
    }
}

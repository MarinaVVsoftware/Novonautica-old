<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2/8/18
 * Time: 13:25
 */

namespace AppBundle\Form\DataTransformer;


use AppBundle\Entity\Contabilidad\Facturacion;
use AppBundle\Entity\Pago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class FacturaPagosDataTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Pasa entidades a un array
     *
     * @param Pago[] $pagos
     * @return mixed The value in the transformed representation
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($pagos)
    {
        if (is_null($pagos) || count($pagos) === 0) {
            return [];
        }

        $data = [];
        foreach ($pagos as $pago) {
            $data[] = '$' . number_format($pago->getCantidad() / 100, 2) . ' ' . $pago->getDivisa();
        }

        return $data;
    }

    /**
     * Pasa un array de entidades a una coleccion
     *
     * @param mixed $values
     * @return mixed The value in the original representation
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform($values)
    {
        if (!is_array($values) || count($values) === 0) {
            return [];
        }

        $qb = $this->em->getRepository('AppBundle:Pago')->createQueryBuilder('p');
        $pagos = $qb->where('p.id IN (:ids)')
            ->setParameter('ids', $values)
            ->getQuery()
            ->getResult();

        $entities = new ArrayCollection($pagos);

        if ($entities->isEmpty()) {
            throw new TransformationFailedException('No se encontraron los pagos');
        }

        return $entities;
    }
}
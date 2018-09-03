<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 1/21/18
 * Time: 00:05
 */

namespace AppBundle\Form\DataTransformer;


use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ClaveUnidadTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Este metodo es llamado cuando se transforma al resultado que se debe ver en el input
     *
     * @param ClaveUnidad $claveUnidad
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($claveUnidad)
    {
        return $claveUnidad === null ? null : $claveUnidad->getClaveUnidad() . ' - ' . $claveUnidad->getNombre();
    }

    /**
     * En este metodo se recibe el string tal cual escrito en el input
     * es aqui donde se debe transformar a una entidad para que symfony pueda usarla
     *
     * @param $claveUnidadString string
     * @return mixed The value in the original representation
     *
     */
    public function reverseTransform($claveUnidadString)
    {
        if (!$claveUnidadString) { return; }

        $claveUnidadCode = explode(' - ', $claveUnidadString);

        $entity = $this->em
            ->getRepository(ClaveUnidad::class)
            ->findOneBy(['claveUnidad' => $claveUnidadCode[0]]);

        if ($entity === null) {
            throw new TransformationFailedException(sprintf(
                'ClaveProdServ con numero "%s" no existe', $claveUnidadString));
        }

        return $entity;
    }
}

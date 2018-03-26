<?php

namespace AppBundle\Entity\Contabilidad\Facturacion\Concepto;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ClaveProdServ
 *
 * @ORM\Table(name="contabilidad_facturacion_concepto_clave_prod_serv")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\Concepto\ClaveProdServRepository")
 */
class ClaveProdServ
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="claveProdServ", type="string", length=20)
     */
    private $claveProdServ;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    public function __toString()
    {
        return $this->claveProdServ;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set claveProdServ
     *
     * @param string $claveProdServ
     *
     * @return ClaveProdServ
     */
    public function setClaveProdServ($claveProdServ)
    {
        $this->claveProdServ = $claveProdServ;

        return $this;
    }

    /**
     * Get claveProdServ
     *
     * @return string
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return ClaveProdServ
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
}


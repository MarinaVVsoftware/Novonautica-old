<?php

namespace AppBundle\Entity\Contabilidad\Facturacion\Concepto;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClaveUnidad
 *
 * @ORM\Table(name="contabilidad_facturacion_concepto_clave_unidad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\Concepto\ClaveUnidadRepository")
 */
class ClaveUnidad
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
     * @ORM\Column(name="clave_unidad", type="string", length=20)
     */
    private $claveUnidad;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    public function __toString()
    {
        return $this->nombre;
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
     * Set claveUnidad
     *
     * @param string $claveUnidad
     *
     * @return ClaveUnidad
     */
    public function setClaveUnidad($claveUnidad)
    {
        $this->claveUnidad = $claveUnidad;

        return $this;
    }

    /**
     * Get claveUnidad
     *
     * @return string
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ClaveUnidad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}


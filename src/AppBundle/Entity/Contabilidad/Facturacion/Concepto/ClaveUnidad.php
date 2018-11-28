<?php

namespace AppBundle\Entity\Contabilidad\Facturacion\Concepto;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ClaveUnidad
 *
 * @ORM\Table(name="contabilidad_facturacion_concepto_clave_unidad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\Concepto\ClaveUnidadRepository")
 */
class ClaveUnidad implements \JsonSerializable
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
     * @ORM\Column(name="clave_unidad", type="string", length=20)
     */
    private $claveUnidad;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
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

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'clave' => $this->claveUnidad,
        ];
    }
}


<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CotizacionNota
 *
 * @ORM\Table(name="cotizacion_nota")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CotizacionNotaRepository")
 */
class CotizacionNota
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
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="fechahoraregistro", type="datetime_immutable")
     */
    private $fechahoraregistro;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", inversedBy="cotizacionnotas")
     */
    private $mhcotizacion;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return CotizacionNota
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechahoraregistro.
     *
     * @param \DateTimeImmutable $fechahoraregistro
     *
     * @return CotizacionNota
     */
    public function setFechahoraregistro($fechahoraregistro)
    {
        $this->fechahoraregistro = $fechahoraregistro;

        return $this;
    }

    /**
     * Get fechahoraregistro.
     *
     * @return \DateTimeImmutable
     */
    public function getFechahoraregistro()
    {
        return $this->fechahoraregistro;
    }

    /**
     * Set mhcotizacion.
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion|null $mhcotizacion
     *
     * @return CotizacionNota
     */
    public function setMhcotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacion = null)
    {
        $this->mhcotizacion = $mhcotizacion;

        return $this;
    }

    /**
     * Get mhcotizacion.
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion|null
     */
    public function getMhcotizacion()
    {
        return $this->mhcotizacion;
    }
}

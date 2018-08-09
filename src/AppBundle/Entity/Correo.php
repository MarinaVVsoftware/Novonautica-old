<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * correo
 *
 * @ORM\Table(name="correo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorreoRepository")
 */
class Correo
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", nullable=true)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="folio_cotizacion", type="text", length=10)
     */
    private $folioCotizacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion")
     * @ORM\JoinColumn(name="idmhcotizacion", referencedColumnName="id")
     */
    private $mhcotizacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion")
     * @ORM\JoinColumn(name="idacotizacion", referencedColumnName="id")
     */
    private $acotizacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Combustible")
     * @ORM\JoinColumn(name="idcombustible", referencedColumnName="id")
     */
    private $combustible;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return correo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     *
     * @return correo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return correo
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

    /**
     * Set folioCotizacion
     *
     * @param string $folioCotizacion
     *
     * @return Correo
     */
    public function setFolioCotizacion($folioCotizacion)
    {
        $this->folioCotizacion = $folioCotizacion;

        return $this;
    }

    /**
     * Get folioCotizacion
     *
     * @return string
     */
    public function getFolioCotizacion()
    {
        return $this->folioCotizacion;
    }

    /**
     * Set mhcotizacion.
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion|null $mhcotizacion
     *
     * @return Correo
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

    /**
     * Set acotizacion.
     *
     * @param \AppBundle\Entity\AstilleroCotizacion|null $acotizacion
     *
     * @return Correo
     */
    public function setAcotizacion(\AppBundle\Entity\AstilleroCotizacion $acotizacion = null)
    {
        $this->acotizacion = $acotizacion;

        return $this;
    }

    /**
     * Get acotizacion.
     *
     * @return \AppBundle\Entity\AstilleroCotizacion|null
     */
    public function getAcotizacion()
    {
        return $this->acotizacion;
    }

    /**
     * Set combustible.
     *
     * @param \AppBundle\Entity\Combustible|null $combustible
     *
     * @return Correo
     */
    public function setCombustible(\AppBundle\Entity\Combustible $combustible = null)
    {
        $this->combustible = $combustible;

        return $this;
    }

    /**
     * Get combustible.
     *
     * @return \AppBundle\Entity\Combustible|null
     */
    public function getCombustible()
    {
        return $this->combustible;
    }
}

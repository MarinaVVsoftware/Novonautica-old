<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Solicitud
 *
 * @ORM\Table(name="tienda_solicitud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\SolicitudRepository")
 */
class Solicitud
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
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="embarcacion", type="string", length=150)
     */
    private $embarcacion;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Peticion", mappedBy="solicitud", cascade={"persist"})
     */
    private $producto;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud_especial", type="string", length=255)
     */
    private $solicitudEspecial;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;

    public function __construct()
    {
        $this->producto = new ArrayCollection();
        $this->estado = 2;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Solicitud
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
     * Set embarcacion
     *
     * @param string $embarcacion
     *
     * @return Solicitud
     */
    public function setEmbarcacion($embarcacion)
    {
        $this->embarcacion = $embarcacion;

        return $this;
    }

    /**
     * Get embarcacion
     *
     * @return string
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }


    /**
     * Set solicitudEspecial
     *
     * @param string $solicitudEspecial
     *
     * @return Solicitud
     */
    public function setSolicitudEspecial($solicitudEspecial)
    {
        $this->solicitudEspecial = $solicitudEspecial;

        return $this;
    }

    /**
     * Get solicitudEspecial
     *
     * @return string
     */
    public function getSolicitudEspecial()
    {
        return $this->solicitudEspecial;
    }

    /**
     * @return int
     */
    public function getEstado(): int
    {
        return $this->estado;
    }

    /**
     * @param int $estado
     */
    public function setEstado(int $estado): void
    {
        $this->estado = $estado;
    }

    /**
     * Add producto
     *
     * @param Peticion $producto
     *
     * @return Solicitud
     */
    public function addProducto(Peticion $producto)
    {
        $producto->setSolicitud($this);
        $this->producto[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param Peticion $producto
     */
    public function removeProducto(Peticion $producto)
    {
        $this->producto->removeElement($producto);
    }

    /**
     * Get producto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducto()
    {
        return $this->producto;
    }
}

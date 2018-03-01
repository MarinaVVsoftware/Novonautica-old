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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="embarcacion")
     */
    private $nombrebarco;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Peticion", mappedBy="solicitud", cascade={"persist"})
     */
    private $producto;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud_especial", type="string", length=255, nullable=true)
     */
    private $solicitudEspecial;

    /**
     * @var integer
     * @ORM\Column(name="preciosolespecial", type="bigint", length=20, nullable=true)
     */
    private $preciosolespecial;

    /**
     * @var integer
     * @ORM\Column(name="subtotal", type="bigint", length=20)
     */
    private $subtotal;

    /**
     * @var integer
     * @ORM\Column(name="total", type="bigint", length=20)
     */
    private $total;

    /**
     * @var int
     *
     * @ORM\Column(name="entregado", type="smallint")
     */
    private $entregado;

    /**
     * @var int
     *
     * @ORM\Column(name="pagado", type="smallint")
     */
    private $pagado;

    public function __construct()
    {
        $this->producto = new ArrayCollection();
        $this->entregado = 2;
        $this->pagado = 2;
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

    /**
     * Set preciosolespecial
     *
     * @param integer $preciosolespecial
     *
     * @return Solicitud
     */
    public function setPreciosolespecial($preciosolespecial)
    {
        $this->preciosolespecial = $preciosolespecial;

        return $this;
    }

    /**
     * Get preciosolespecial
     *
     * @return integer
     */
    public function getPreciosolespecial()
    {
        return $this->preciosolespecial;
    }

    /**
     * Set nombrebarco
     *
     * @param \AppBundle\Entity\Barco $nombrebarco
     *
     * @return Solicitud
     */
    public function setNombrebarco(\AppBundle\Entity\Barco $nombrebarco = null)
    {
        $this->nombrebarco = $nombrebarco;

        return $this;
    }

    /**
     * Get nombrebarco
     *
     * @return \AppBundle\Entity\Barco
     */
    public function getNombrebarco()
    {
        return $this->nombrebarco;
    }

    /**
     * Set subtotal
     *
     * @param integer $subtotal
     *
     * @return Solicitud
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return integer
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Solicitud
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set entregado.
     *
     * @param int $entregado
     *
     * @return Solicitud
     */
    public function setEntregado($entregado)
    {
        $this->entregado = $entregado;

        return $this;
    }

    /**
     * Get entregado.
     *
     * @return int
     */
    public function getEntregado()
    {
        return $this->entregado;
    }

    /**
     * Set pagado.
     *
     * @param int $pagado
     *
     * @return Solicitud
     */
    public function setPagado($pagado)
    {
        $this->pagado = $pagado;

        return $this;
    }

    /**
     * Get pagado.
     *
     * @return int
     */
    public function getPagado()
    {
        return $this->pagado;
    }
}

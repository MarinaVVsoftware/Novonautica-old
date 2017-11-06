<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarinaHumedaCotizaServicios
 *
 * @ORM\Table(name="marina_humeda_cotiza_servicios")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaCotizaServiciosRepository")
 */
class MarinaHumedaCotizaServicios
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="float", nullable=true)
     */
    private $precio;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=true)
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", inversedBy="mhcservicios")
     * @ORM\JoinColumn(name="idmhcotizacion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $marinahumedacotizacion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaServicio")
     * @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     */
    private $marinahumedaservicio;

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
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precio
     *
     * @param float $precio
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return float
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set iva
     *
     * @param float $iva
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set descuento
     *
     * @param float $descuento
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setMarinaHumedaCotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion = null)
    {
        $this->marinahumedacotizacion = $marinahumedacotizacion;
        return $this;
    }

    /**
     * Get marinahumedacotizacion
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion
     */
    public function getMarinaHumedaCotizacion()
    {
        return $this->marinahumedacotizacion;
    }

    /**
     * Set marinahumedaservicio
     *
     * @param \AppBundle\Entity\MarinaHumedaServicio $marinahumedaservicio
     *
     * @return MarinaHumedaServicio
     */
    public function setMarinaHumedaServicio(\AppBundle\Entity\MarinaHumedaServicio $marinahumedaservicio = null)
    {
        $this->marinahumedaservicio = $marinahumedaservicio;
        return $this;
    }

    /**
     * Get marinahumedaservicio
     *
     * @return \AppBundle\Entity\MarinaHumedaServicio
     */
    public function getMarinaHumedaServicio()
    {
        return $this->marinahumedaservicio;
    }


}
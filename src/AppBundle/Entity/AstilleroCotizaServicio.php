<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AstilleroCotizaServicio
 *
 * @ORM\Table(name="astillero_cotiza_servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AstilleroCotizaServicioRepository")
 */
class AstilleroCotizaServicio
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
     * @ORM\Column(name="otroservicio", type="string", length=255, nullable=true)
     */
    private $otroservicio;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="integer", nullable=true)
     */
    private $precio;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="iva", type="bigint", nullable=true)
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="acservicios")
     * @ORM\JoinColumn(name="idastillerocotizacion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $astillerocotizacion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroServicioBasico")
     * @ORM\JoinColumn(name="idserviciobasico", referencedColumnName="id")
     */
    private $astilleroserviciobasico;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Producto", inversedBy="ACotizacionesServicios")
     * @ORM\JoinColumn(name="idproducto", referencedColumnName="id")
     */
    private $producto;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Servicio")
     * @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     */
    private $servicio;

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
     * Set otroservicio
     *
     * @param string $otroservicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setOtroservicio($otroservicio)
    {
        $this->otroservicio = $otroservicio;

        return $this;
    }

    /**
     * Get otroservicio
     *
     * @return string
     */
    public function getOtroservicio()
    {
        return $this->otroservicio;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return AstilleroCotizaServicio
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
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return AstilleroCotizaServicio
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
     * Set astillerocotizacion
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astillerocotizacion
     *
     * @return AstilleroCotizaServicio
     */
    public function setAstillerocotizacion(\AppBundle\Entity\AstilleroCotizacion $astillerocotizacion = null)
    {
        $this->astillerocotizacion = $astillerocotizacion;

        return $this;
    }

    /**
     * Get astillerocotizacion
     *
     * @return \AppBundle\Entity\AstilleroCotizacion
     */
    public function getAstillerocotizacion()
    {
        return $this->astillerocotizacion;
    }

    /**
     * Set astilleroserviciobasico
     *
     * @param \AppBundle\Entity\AstilleroServicioBasico $astilleroservicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setAstilleroserviciobasico(\AppBundle\Entity\AstilleroServicioBasico $astilleroserviciobasico = null)
    {
        $this->astilleroserviciobasico = $astilleroserviciobasico;

        return $this;
    }

    /**
     * Get astilleroserviciobasico
     *
     * @return \AppBundle\Entity\AstilleroServicioBasico
     */
    public function getAstilleroserviciobasico()
    {
        return $this->astilleroserviciobasico;
    }


    /**
     * Set producto
     *
     * @param \AppBundle\Entity\Astillero\Producto $producto
     *
     * @return AstilleroCotizaServicio
     */
    public function setProducto(\AppBundle\Entity\Astillero\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \AppBundle\Entity\Astillero\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set servicio
     *
     * @param \AppBundle\Entity\Astillero\Servicio $servicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setServicio(\AppBundle\Entity\Astillero\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \AppBundle\Entity\Astillero\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param int $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param int $subtotal
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }

    /**
     * @return int
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param int $iva
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }
}

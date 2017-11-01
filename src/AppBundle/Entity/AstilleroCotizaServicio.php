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
     * @ORM\Column(name="servicio", type="string", length=255, nullable=true)
     */
    private $servicio;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="acservicios")
     * @ORM\JoinColumn(name="idastillerocotizacion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $astillerocotizacion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroServicio")
     * @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     */
    private $astilleroservicio;

    /**
     * Many cotizaciones have Many Productos.
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Producto", inversedBy="acservicios")
     * @ORM\JoinTable(name="astillero_cotizaciones_productos")
     */
    private $productos;

    public function __construct() {
        $this->productos = new ArrayCollection();
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
     * Set servicio
     *
     * @param string $servicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return string
     */
    public function getServicio()
    {
        return $this->servicio;
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
     * Set precio
     *
     * @param float $precio
     *
     * @return AstilleroCotizaServicio
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
     * @return AstilleroCotizaServicio
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
     * @return AstilleroCotizaServicio
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
     * Set total
     *
     * @param float $total
     *
     * @return AstilleroCotizaServicio
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
     * Set astilleroservicio
     *
     * @param \AppBundle\Entity\AstilleroServicio $astilleroservicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setAstilleroservicio(\AppBundle\Entity\AstilleroServicio $astilleroservicio = null)
    {
        $this->astilleroservicio = $astilleroservicio;

        return $this;
    }

    /**
     * Get astilleroservicio
     *
     * @return \AppBundle\Entity\AstilleroServicio
     */
    public function getAstilleroservicio()
    {
        return $this->astilleroservicio;
    }

    /**
     * Add producto
     *
     * @param \AppBundle\Entity\Producto $producto
     *
     * @return AstilleroCotizaServicio
     */
    public function addProducto(\AppBundle\Entity\Producto $producto)
    {
        $this->productos[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param \AppBundle\Entity\Producto $producto
     */
    public function removeProducto(\AppBundle\Entity\Producto $producto)
    {
        $this->productos->removeElement($producto);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductos()
    {
        return $this->productos;
    }
}

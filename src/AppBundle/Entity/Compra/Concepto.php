<?php

namespace AppBundle\Entity\Compra;

use AppBundle\Entity\Compra;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Astillero\Proveedor;

/**
 * Concepto
 *
 * @ORM\Table(name="compra_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Compra\ConceptoRepository")
 */
class Concepto
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
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="ivatotal", type="bigint")
     */
    private $ivatotal;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var Compra
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Compra", inversedBy="conceptos")
     */
    private $compra;

    /**
     * @var \AppBundle\Entity\MarinaHumedaServicio
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaServicio")
     */
    private $marinaServicio;

    /**
     * @var \AppBundle\Entity\Combustible\Catalogo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Combustible\Catalogo")
     */
    private $combustibleCatalogo;

    /**
     * @var \AppBundle\Entity\Astillero\Producto
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Producto")
     */
    private $astilleroProducto;

    /**
     * @var \AppBundle\Entity\Tienda\Producto
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Producto")
     */
    private $tiendaProducto;

    /**
     * @var Proveedor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Proveedor")
     */
    private $proveedor;

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
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return Concepto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precio.
     *
     * @param int $precio
     *
     * @return Concepto
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set subtotal.
     *
     * @param int $subtotal
     *
     * @return Concepto
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal.
     *
     * @param int $ivatotal
     *
     * @return Concepto
     */
    public function setIvatotal($ivatotal)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal.
     *
     * @return int
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Concepto
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set compra.
     *
     * @param \AppBundle\Entity\Compra|null $compra
     *
     * @return Concepto
     */
    public function setCompra(\AppBundle\Entity\Compra $compra = null)
    {
        $this->compra = $compra;

        return $this;
    }

    /**
     * Get compra.
     *
     * @return \AppBundle\Entity\Compra|null
     */
    public function getCompra()
    {
        return $this->compra;
    }

    /**
     * Set marinaServicio.
     *
     * @param \AppBundle\Entity\MarinaHumedaServicio|null $marinaServicio
     *
     * @return Concepto
     */
    public function setMarinaServicio(\AppBundle\Entity\MarinaHumedaServicio $marinaServicio = null)
    {
        $this->marinaServicio = $marinaServicio;

        return $this;
    }

    /**
     * Get marinaServicio.
     *
     * @return \AppBundle\Entity\MarinaHumedaServicio|null
     */
    public function getMarinaServicio()
    {
        return $this->marinaServicio;
    }

    /**
     * Set combustibleCatalogo.
     *
     * @param \AppBundle\Entity\Combustible\Catalogo|null $combustibleCatalogo
     *
     * @return Concepto
     */
    public function setCombustibleCatalogo(\AppBundle\Entity\Combustible\Catalogo $combustibleCatalogo = null)
    {
        $this->combustibleCatalogo = $combustibleCatalogo;

        return $this;
    }

    /**
     * Get combustibleCatalogo.
     *
     * @return \AppBundle\Entity\Combustible\Catalogo|null
     */
    public function getCombustibleCatalogo()
    {
        return $this->combustibleCatalogo;
    }

    /**
     * Set astilleroProducto.
     *
     * @param \AppBundle\Entity\Astillero\Producto|null $astilleroProducto
     *
     * @return Concepto
     */
    public function setAstilleroProducto(\AppBundle\Entity\Astillero\Producto $astilleroProducto = null)
    {
        $this->astilleroProducto = $astilleroProducto;

        return $this;
    }

    /**
     * Get astilleroProducto.
     *
     * @return \AppBundle\Entity\Astillero\Producto|null
     */
    public function getAstilleroProducto()
    {
        return $this->astilleroProducto;
    }

    /**
     * Set tiendaProducto.
     *
     * @param \AppBundle\Entity\Tienda\Producto|null $tiendaProducto
     *
     * @return Concepto
     */
    public function setTiendaProducto(\AppBundle\Entity\Tienda\Producto $tiendaProducto = null)
    {
        $this->tiendaProducto = $tiendaProducto;

        return $this;
    }

    /**
     * Get tiendaProducto.
     *
     * @return \AppBundle\Entity\Tienda\Producto|null
     */
    public function getTiendaProducto()
    {
        return $this->tiendaProducto;
    }

    /**
     * Set proveedor.
     *
     * @param \AppBundle\Entity\Astillero\Proveedor|null $proveedor
     *
     * @return Concepto
     */
    public function setProveedor(\AppBundle\Entity\Astillero\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return \AppBundle\Entity\Astillero\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
}

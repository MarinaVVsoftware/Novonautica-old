<?php

namespace AppBundle\Entity\Tienda\Venta;

use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Venta;
use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="tienda_venta_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\Venta\ConceptoRepository")
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
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="precio_unitario", type="bigint")
     */
    private $precioUnitario;

    /**
     * @var int
     *
     * @ORM\Column(name="iva", type="bigint")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="descuento", type="bigint")
     */
    private $descuento;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var Venta
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Venta", inversedBy="conceptos")
     */
    private $venta;

    /**
     * @var Producto
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Producto")
     */
    private $producto;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param int $precioUnitario
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;
    }

    /**
     * @return int
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
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
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param int $descuento
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    }

    /**
     * @return int
     */
    public function getDescuento()
    {
        return $this->descuento;
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
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param Venta|null $venta
     */
    public function setVenta(Venta $venta = null)
    {
        $this->venta = $venta;
    }

    /**
     * @return Venta|null
     */
    public function getVenta()
    {
        return $this->venta;
    }

    /**
     * @return Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * @param Producto $producto
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;
    }
}

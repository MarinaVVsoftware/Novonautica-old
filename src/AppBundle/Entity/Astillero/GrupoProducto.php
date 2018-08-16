<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrupoProducto
 *
 * @ORM\Table(name="astillero_grupo_producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\GrupoProductoRepository")
 */
class GrupoProducto
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
     * 0 = cantidad fija, 1 = promedio por pie
     *
     * @var int
     *
     * @ORM\Column(name="tipo_cantidad", type="smallint")
     */
    private $tipoCantidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var Servicio
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Astillero\Servicio", mappedBy="gruposProductos")
     */
    private $servicio;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Producto")
     * @ORM\JoinColumn(name="idproducto", referencedColumnName="id")
     */
    private $producto;

    public function __toString()
    {
        return $this->producto;
    }

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
     * @return GrupoProducto
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
     * Set observaciones.
     *
     * @param string|null $observaciones
     *
     * @return GrupoProducto
     */
    public function setObservaciones($observaciones = null)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones.
     *
     * @return string|null
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set producto.
     *
     * @param \AppBundle\Entity\Astillero\Producto|null $producto
     *
     * @return GrupoProducto
     */
    public function setProducto(\AppBundle\Entity\Astillero\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return \AppBundle\Entity\Astillero\Producto|null
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set tipoCantidad.
     *
     * @param int $tipoCantidad
     *
     * @return GrupoProducto
     */
    public function setTipoCantidad($tipoCantidad)
    {
        $this->tipoCantidad = $tipoCantidad;

        return $this;
    }

    /**
     * Get tipoCantidad.
     *
     * @return int
     */
    public function getTipoCantidad()
    {
        return $this->tipoCantidad;
    }

    /**
     * @return Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @param Servicio $servicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }
}

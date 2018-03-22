<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;

/**
 * Peticion
 *
 * @ORM\Table(name="tienda_peticion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\PeticionRepository")
 */
class Peticion
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Producto", inversedBy="nombreproducto")
     */
    private $producto;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Solicitud", inversedBy="producto")
     * @ORM\JoinColumn(name="solicitud_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $solicitud;

    /**
     * @var integer
     * @ORM\Column(name="cantidad", type="integer", length=255)
     */
    private $cantidad;

    /**
     * @var integer
     * @ORM\Column(name="cantidad_entregado", type="integer", length=255)
     */
    private $cantidad_entregado;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

//    /**
//     * Set peticion
//     *
//     * @param string $peticion
//     *
//     * @return Peticion
//     */
//    public function setPeticion($peticion)
//    {
//        $this->peticion = $peticion;
//
//        return $this;
//    }
//
//    /**
//     * Get peticion
//     *
//     * @return string
//     */
//    public function getPeticion()
//    {
//        return $this->peticion;
//    }

    /**
     * Set solicitud
     *
     * @param Solicitud $solicitud
     *
     * @return Peticion
     */
    public function setSolicitud(Solicitud $solicitud = null)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud
     *
     * @return Solicitud
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return Peticion
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set cantidadEntregado.
     *
     * @param int $cantidadEntregado
     *
     * @return Peticion
     */
    public function setCantidadEntregado($cantidadEntregado)
    {
        $this->cantidad_entregado = $cantidadEntregado;

        return $this;
    }

    /**
     * Get cantidadEntregado.
     *
     * @return int
     */
    public function getCantidadEntregado()
    {
        return $this->cantidad_entregado;
    }

    /**
     * Set producto.
     *
     * @param Producto|null $producto
     *
     * @return Peticion
     */
    public function setProducto(Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return Producto|null
     */
    public function getProducto()
    {
        return $this->producto;
    }
}

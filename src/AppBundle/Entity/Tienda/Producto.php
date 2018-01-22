<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="tienda_producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\ProductoRepository")
 */
class Producto
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
     * @ORM\Column(name="solicitud", type="string", length=100)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="precio", type="bigint", nullable=true)
     */
    private $precio;

    public function __toString()
    {
        return $this->nombre;
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

//    /**
//     * @return Solicitud
//     */
//    public function getSolicitud()
//    {
//        return $this->solicitud;
//    }
//
//    /**
//     * @param Solicitud $solicitud
//     *
//     * @return Producto
//     */
//    public function setSolicitud(Solicitud $solicitud = null)
//    {
//        $this->solicitud = $solicitud;
//        return $this;
//    }

    /**
     * Set precio
     *
     * @param integer $precio
     *
     * @return Producto
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return integer
     */
    public function getPrecio()
    {
        return $this->precio;
    }
}

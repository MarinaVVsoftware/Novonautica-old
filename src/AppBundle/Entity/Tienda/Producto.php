<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Peticion", mappedBy="peticion")
     */
    private $nombreproducto;

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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nombreproducto = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add nombreproducto
     *
     * @param \AppBundle\Entity\Tienda\Peticion $nombreproducto
     *
     * @return Producto
     */
    public function addNombreproducto(\AppBundle\Entity\Tienda\Peticion $nombreproducto)
    {
        $this->nombreproducto[] = $nombreproducto;

        return $this;
    }

    /**
     * Remove nombreproducto
     *
     * @param \AppBundle\Entity\Tienda\Peticion $nombreproducto
     */
    public function removeNombreproducto(\AppBundle\Entity\Tienda\Peticion $nombreproducto)
    {
        $this->nombreproducto->removeElement($nombreproducto);
    }

    /**
     * Get nombreproducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNombreproducto()
    {
        return $this->nombreproducto;
    }
}

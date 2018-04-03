<?php

namespace AppBundle\Entity\Astillero;

use AppBundle\Entity\AstilleroCotizaServicio;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Producto
 *
 * @ORM\Table(name="astillero_producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ProductoRepository")
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
     * @Assert\NotBlank(message="Es requirido un id. de articulo")
     *
     * @ORM\Column(name="identificador", type="string", length=50)
     */
    private $identificador;

    /**
     * @var
     *
     * @Assert\NotBlank(message="Proveedor no puede quedar vacío")
     *
     * @ORM\Column(name="proveedor", type="string", length=50)
     */
    private $proveedor;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     * @Assert\NotBlank(message="Nombre no puede quedar vacío")
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var int
     * @Assert\NotBlank(message="Precio no puede quedar vacío")
     *
     * @ORM\Column(name="precio", type="bigint", nullable=true)
     */
    private $precio;

    /**
     * @var string
     * @Assert\NotBlank(message="Unidad no puede quedar vacío")
     *
     * @ORM\Column(name="unidad", type="string", length=20, nullable=true)
     */
    private $unidad;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizaServicio", mappedBy="producto")
     */
    private $ACotizacionesServicios;

    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ACotizacionesServicios = new ArrayCollection();
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
     * Set identificador.
     *
     * @param string $identificador
     *
     * @return Producto
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * Get identificador.
     *
     * @return string
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set proveedor.
     *
     * @param string $proveedor
     *
     * @return Producto
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return string
     */
    public function getProveedor()
    {
        return $this->proveedor;
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
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     *
     * @return Producto
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Add aCotizacionesServicio
     *
     * @param AstilleroCotizaServicio $aCotizacionesServicio
     *
     * @return Producto
     */
    public function addACotizacionesServicio(AstilleroCotizaServicio $aCotizacionesServicio)
    {
        $this->ACotizacionesServicios[] = $aCotizacionesServicio;

        return $this;
    }

    /**
     * Remove aCotizacionesServicio
     *
     * @param AstilleroCotizaServicio $aCotizacionesServicio
     */
    public function removeACotizacionesServicio(AstilleroCotizaServicio $aCotizacionesServicio)
    {
        $this->ACotizacionesServicios->removeElement($aCotizacionesServicio);
    }

    /**
     * Get aCotizacionesServicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getACotizacionesServicios()
    {
        return $this->ACotizacionesServicios;
    }
}

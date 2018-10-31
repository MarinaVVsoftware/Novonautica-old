<?php

namespace AppBundle\Entity\Astillero;

use AppBundle\Entity\AstilleroCotizaServicio;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;

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
     * @var float
     *
     * @ORM\Column(name="existencia", type="float", nullable=true)
     */
    private $existencia;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizaServicio", mappedBy="producto")
     */
    private $ACotizacionesServicios;

    /**
     * @var ClaveUnidad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

    /**
     * @var ClaveProdServ
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Astillero\Proveedor")
     * @ORM\JoinTable(
     *     name="astillero_productos_x_proveedores",
     *     joinColumns={@ORM\JoinColumn(name="producto_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="proveedor_id", referencedColumnName="id")}
     * )
     */
    private $proveedores;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ACotizacionesServicios = new ArrayCollection();
        $this->proveedores = new ArrayCollection();
    }

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

    /**
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     *
     * @return Producto
     */
    public function setClaveUnidad(ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;

        return $this;
    }

    /**
     * Get claveUnidad.
     *
     * @return ClaveUnidad|null
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set claveProdServ.
     *
     * @param ClaveProdServ|null $claveProdServ
     *
     * @return Producto
     */
    public function setClaveProdServ(ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;

        return $this;
    }

    /**
     * Get claveProdServ.
     *
     * @return ClaveProdServ|null
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set existencia.
     *
     * @param float|null $existencia
     *
     * @return Producto
     */
    public function setExistencia($existencia = null)
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia.
     *
     * @return float|null
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Add proveedor.
     *
     * @param Proveedor $proveedores
     *
     * @return Producto
     */
    public function addProveedore(Proveedor $proveedores)
    {
        $this->proveedores[] = $proveedores;

        return $this;
    }

    /**
     * Remove proveedor.
     *
     * @param Proveedor $proveedores
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProveedore(Proveedor $proveedores)
    {
        return $this->proveedores->removeElement($proveedores);
    }

    /**
     * Get proveedores.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProveedores()
    {
        return $this->proveedores;
    }
}

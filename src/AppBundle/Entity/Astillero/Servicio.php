<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;

/**
 * Servicio
 *
 * @ORM\Table(name="astillero_servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ServicioRepository")
 */
class Servicio
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
     * @Groups({"facturacion"})
     * @Assert\NotBlank(
     *     message="Nombre no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var int
     * @Assert\NotBlank(
     *     message="Precio no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="precio", type="bigint", nullable=true)
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Unidad no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="unidad", type="string", length=20, nullable=true)
     */
    private $unidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\Astillero\GrupoProducto",
     *     inversedBy="servicio",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(
     *     name="servicios_gruposproductos",
     *     joinColumns={
     *      @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *      @ORM\JoinColumn(name="idgrupoproducto", referencedColumnName="id", unique=true)
     *     }
     *     )
     */
    private $gruposProductos;

    /**
     * Variable para identificar si se tomara la cantidad el servicio basado en el eslora
     *
     * @var bool
     *
     * @ORM\Column(name="tipo_cantidad", type="boolean")
     */
    private $tipoCantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_descuento", type="integer")
     */
    private $diasDescuento;

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
     * Constructor
     */
    public function __construct()
    {
        $this->gruposProductos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Servicio
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
     * @return Servicio
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
     * @return Servicio
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Servicio
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set divisa.
     *
     * @param string $divisa
     *
     * @return Servicio
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * Add gruposProducto.
     *
     * @param \AppBundle\Entity\Astillero\GrupoProducto $gruposProducto
     *
     * @return Servicio
     */
    public function addGruposProducto(\AppBundle\Entity\Astillero\GrupoProducto $gruposProducto)
    {
        $this->gruposProductos[] = $gruposProducto;

        return $this;
    }

    /**
     * Remove gruposProducto.
     *
     * @param \AppBundle\Entity\Astillero\GrupoProducto $gruposProducto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGruposProducto(\AppBundle\Entity\Astillero\GrupoProducto $gruposProducto)
    {
        return $this->gruposProductos->removeElement($gruposProducto);
    }

    /**
     * Get gruposProductos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGruposProductos()
    {
        return $this->gruposProductos;
    }

    /**
     * Set tipoCantidad.
     *
     * @param bool $tipoCantidad
     *
     * @return Servicio
     */
    public function setTipoCantidad($tipoCantidad)
    {
        $this->tipoCantidad = $tipoCantidad;

        return $this;
    }

    /**
     * Get tipoCantidad.
     *
     * @return bool
     */
    public function getTipoCantidad()
    {
        return $this->tipoCantidad;
    }

    /**
     * Set diasDescuento.
     *
     * @param int $diasDescuento
     *
     * @return Servicio
     */
    public function setDiasDescuento($diasDescuento)
    {
        $this->diasDescuento = $diasDescuento;

        return $this;
    }

    /**
     * Get diasDescuento.
     *
     * @return int
     */
    public function getDiasDescuento()
    {
        return $this->diasDescuento;
    }

    /**
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     *
     * @return Servicio
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
     * @return Servicio
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
}

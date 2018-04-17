<?php

namespace AppBundle\Entity\Tienda;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="preciocolaborador", type="bigint")
     */
    private $preciocolaborador;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barras", type="string", length=30)
     */
    private $codigoBarras;

    /**
     * @var ClaveProdServ
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

    /**
     * @var ClaveUnidad
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Peticion", mappedBy="producto")
     */
    private $nombreproducto;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nombreproducto = new ArrayCollection();
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
     * Get nombreproducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNombreproducto()
    {
        return $this->nombreproducto;
    }

    /**
     * Add nombreproducto
     *
     * @param Peticion $nombreproducto
     *
     * @return Producto
     */
    public function addNombreproducto(Peticion $nombreproducto)
    {
        $this->nombreproducto[] = $nombreproducto;

        return $this;
    }

    /**
     * Remove nombreproducto
     *
     * @param Peticion $nombreproducto
     */
    public function removeNombreproducto(Peticion $nombreproducto)
    {
        $this->nombreproducto->removeElement($nombreproducto);
    }

    /**
     * Set preciocolaborador
     *
     * @param integer $preciocolaborador
     *
     * @return Producto
     */
    public function setPreciocolaborador($preciocolaborador)
    {
        $this->preciocolaborador = $preciocolaborador;

        return $this;
    }

    /**
     * Get preciocolaborador
     *
     * @return integer
     */
    public function getPreciocolaborador()
    {
        return $this->preciocolaborador;
    }

    /**
     * @return string
     */
    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    /**
     * @param string $codigoBarras
     */
    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;
    }
}

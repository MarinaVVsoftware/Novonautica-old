<?php

namespace AppBundle\Entity\Solicitud;

use AppBundle\Entity\Astillero\Producto;
use AppBundle\Entity\MarinaHumedaServicio;
use AppBundle\Entity\Solicitud;
use Doctrine\ORM\Mapping as ORM;


/**
 * Concepto
 *
 * @ORM\Table(name="solicitud_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Solicitud\ConceptoRepository")
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
     * @var string
     *
     * @ORM\Column(name="nota", type="string", length=255, nullable=true)
     */
    private $nota;

    /**
     * @var bool
     *
     * @ORM\Column(name="solicitado", type="boolean")
     */
    private $solicitado;

    /**
     * @var Solicitud
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Solicitud", inversedBy="conceptos")
     */
    private $solicitud;

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
     * Constructor
     */
    public function __construct()
    {
        $this->solicitado = false;
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
     * Set solicitado.
     *
     * @param bool $solicitado
     *
     * @return Concepto
     */
    public function setSolicitado($solicitado)
    {
        $this->solicitado = $solicitado;

        return $this;
    }

    /**
     * Get solicitado.
     *
     * @return bool
     */
    public function getSolicitado()
    {
        return $this->solicitado;
    }

    /**
     * Set solicitud.
     *
     * @param \AppBundle\Entity\Solicitud|null $solicitud
     *
     * @return Concepto
     */
    public function setSolicitud(\AppBundle\Entity\Solicitud $solicitud = null)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud.
     *
     * @return \AppBundle\Entity\Solicitud|null
     */
    public function getSolicitud()
    {
        return $this->solicitud;
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
     * Set nota.
     *
     * @param string|null $nota
     *
     * @return Concepto
     */
    public function setNota($nota = null)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota.
     *
     * @return string|null
     */
    public function getNota()
    {
        return $this->nota;
    }
}

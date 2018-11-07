<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Solicitud\Concepto;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Solicitud
 *
 * @ORM\Table(name="solicitud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SolicitudRepository")
 */
class Solicitud
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="folio", type="integer", length=255)
     */
    private $folio;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="validado_compra", type="boolean", nullable=true)
     */
    private $validadoCompra;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_valido_compra", type="string", length=255, nullable=true)
     */
    private $nombreValidoCompra;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_valido_compra", type="datetime", nullable=true)
     */
    private $fechaValidoCompra;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="validado_almacen", type="boolean", nullable=true)
     */
    private $validadoAlmacen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_valido_almacen", type="string", length=255, nullable=true)
     */
    private $nombreValidoAlmacen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_valido_almacen", type="datetime", nullable=true)
     */
    private $fechaValidoAlmacen;

    /**
     * @var string
     *
     * @ORM\Column(name="nota_solicitud", type="text", nullable=true)
     */
    private $notaSolicitud;

    /**
     * @var string
     *
     * @ORM\Column(name="nota_compra", type="text", nullable=true)
     */
    private $notaCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="nota_almacen", type="text", nullable=true)
     */
    private $notaAlmacen;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=true)
     */
    private $iva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ivatotal", type="bigint", nullable=true)
     */
    private $ivatotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referencia", type="string", length=255, nullable=true)
     */
    private $referencia;

    /**
     * @var Emisor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

    /**
     * @var Concepto
     *
     * @Assert\Valid()
     * @Assert\Count(
     *     min="1",
     *     minMessage="Debes incluir al menos un conepto para realizar un registro",
     * )
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Solicitud\Concepto", mappedBy="solicitud", cascade={"persist"})
     */
    private $conceptos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conceptos = new ArrayCollection();
        $this->validadoCompra = false;
        $this->validadoAlmacen = false;

    }

    public function __toString()
    {
        return $this->folio.'';
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
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Solicitud
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set empresa.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null $empresa
     *
     * @return Solicitud
     */
    public function setEmpresa(Emisor $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa.
     *
     * @return \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set creador.
     *
     * @param \AppBundle\Entity\Usuario|null $creador
     *
     * @return Solicitud
     */
    public function setCreador(Usuario $creador = null)
    {
        $this->creador = $creador;

        return $this;
    }

    /**
     * Get creador.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * Add concepto.
     *
     * @param \AppBundle\Entity\Solicitud\Concepto $concepto
     *
     * @return Solicitud
     */
    public function addConcepto(Concepto $concepto)
    {
        $concepto->setSolicitud($this);
        $this->conceptos[] = $concepto;

        return $this;
    }

    /**
     * Remove concepto.
     *
     * @param \AppBundle\Entity\Solicitud\Concepto $concepto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeConcepto(\AppBundle\Entity\Solicitud\Concepto $concepto)
    {
        return $this->conceptos->removeElement($concepto);
    }

    /**
     * Get conceptos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }

    /**
     * Set folio.
     *
     * @param int $folio
     *
     * @return Solicitud
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio.
     *
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set iva.
     *
     * @param float|null $iva
     *
     * @return Solicitud
     */
    public function setIva($iva = null)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return float|null
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal.
     *
     * @param int|null $subtotal
     *
     * @return Solicitud
     */
    public function setSubtotal($subtotal = null)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int|null
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal.
     *
     * @param int|null $ivatotal
     *
     * @return Solicitud
     */
    public function setIvatotal($ivatotal = null)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal.
     *
     * @return int|null
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total.
     *
     * @param int|null $total
     *
     * @return Solicitud
     */
    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set notaSolicitud.
     *
     * @param string|null $notaSolicitud
     *
     * @return Solicitud
     */
    public function setNotaSolicitud($notaSolicitud = null)
    {
        $this->notaSolicitud = $notaSolicitud;

        return $this;
    }

    /**
     * Get notaSolicitud.
     *
     * @return string|null
     */
    public function getNotaSolicitud()
    {
        return $this->notaSolicitud;
    }

    /**
     * Set notaCompra.
     *
     * @param string|null $notaCompra
     *
     * @return Solicitud
     */
    public function setNotaCompra($notaCompra = null)
    {
        $this->notaCompra = $notaCompra;

        return $this;
    }

    /**
     * Get notaCompra.
     *
     * @return string|null
     */
    public function getNotaCompra()
    {
        return $this->notaCompra;
    }

    /**
     * Set validadoCompra.
     *
     * @param bool|null $validadoCompra
     *
     * @return Solicitud
     */
    public function setValidadoCompra($validadoCompra = null)
    {
        $this->validadoCompra = $validadoCompra;

        return $this;
    }

    /**
     * Get validadoCompra.
     *
     * @return bool|null
     */
    public function getValidadoCompra()
    {
        return $this->validadoCompra;
    }

    /**
     * Set nombreValidoCompra.
     *
     * @param string|null $nombreValidoCompra
     *
     * @return Solicitud
     */
    public function setNombreValidoCompra($nombreValidoCompra = null)
    {
        $this->nombreValidoCompra = $nombreValidoCompra;

        return $this;
    }

    /**
     * Get nombreValidoCompra.
     *
     * @return string|null
     */
    public function getNombreValidoCompra()
    {
        return $this->nombreValidoCompra;
    }

    /**
     * Set validadoAlmacen.
     *
     * @param bool|null $validadoAlmacen
     *
     * @return Solicitud
     */
    public function setValidadoAlmacen($validadoAlmacen = null)
    {
        $this->validadoAlmacen = $validadoAlmacen;

        return $this;
    }

    /**
     * Get validadoAlmacen.
     *
     * @return bool|null
     */
    public function getValidadoAlmacen()
    {
        return $this->validadoAlmacen;
    }

    /**
     * Set nombreValidoAlmacen.
     *
     * @param string|null $nombreValidoAlmacen
     *
     * @return Solicitud
     */
    public function setNombreValidoAlmacen($nombreValidoAlmacen = null)
    {
        $this->nombreValidoAlmacen = $nombreValidoAlmacen;

        return $this;
    }

    /**
     * Get nombreValidoAlmacen.
     *
     * @return string|null
     */
    public function getNombreValidoAlmacen()
    {
        return $this->nombreValidoAlmacen;
    }

    /**
     * Set referencia.
     *
     * @param string|null $referencia
     *
     * @return Solicitud
     */
    public function setReferencia($referencia = null)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia.
     *
     * @return string|null
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * Set notaAlmacen.
     *
     * @param string|null $notaAlmacen
     *
     * @return Solicitud
     */
    public function setNotaAlmacen($notaAlmacen = null)
    {
        $this->notaAlmacen = $notaAlmacen;

        return $this;
    }

    /**
     * Get notaAlmacen.
     *
     * @return string|null
     */
    public function getNotaAlmacen()
    {
        return $this->notaAlmacen;
    }

    /**
     * Set fechaValidoCompra.
     *
     * @param \DateTime|null $fechaValidoCompra
     *
     * @return Solicitud
     */
    public function setFechaValidoCompra($fechaValidoCompra = null)
    {
        $this->fechaValidoCompra = $fechaValidoCompra;

        return $this;
    }

    /**
     * Get fechaValidoCompra.
     *
     * @return \DateTime|null
     */
    public function getFechaValidoCompra()
    {
        return $this->fechaValidoCompra;
    }

    /**
     * Set fechaValidoAlmacen.
     *
     * @param \DateTime|null $fechaValidoAlmacen
     *
     * @return Solicitud
     */
    public function setFechaValidoAlmacen($fechaValidoAlmacen = null)
    {
        $this->fechaValidoAlmacen = $fechaValidoAlmacen;

        return $this;
    }

    /**
     * Get fechaValidoAlmacen.
     *
     * @return \DateTime|null
     */
    public function getFechaValidoAlmacen()
    {
        return $this->fechaValidoAlmacen;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MarinaHumedaCotizacionAdicional
 *
 * @ORM\Table(name="marina_humeda_cotizacion_adicional")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaCotizacionAdicionalRepository")
 * @ORM\EntityListeners({"AppBundle\Entity\Marina\CotizacionAdicionalListener"})
 */
class MarinaHumedaCotizacionAdicional
{
    const NADA = 0;
    const EXENTO = 1;
    const TASA_CERO = 2;
    const IVA = 3;

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
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var integer
     *
     * @ORM\Column(name="dolar", type="smallint")
     */
    private $dolar;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="ivatotal", type="bigint", nullable=true)
     */
    private $ivatotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="descuentototal", type="bigint", nullable=true)
     */
    private $descuentototal;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="integer", nullable=true)
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="mhcotizacionesadicionales")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="mhcotizacionesadicionales")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizaServicios", mappedBy="marinahumedacotizacionadicional",cascade={"persist"})
     */
    private $mhcservicios;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    private static $tipoList = [
        MarinaHumedaCotizacionAdicional::NADA => 'Sin definir',
        MarinaHumedaCotizacionAdicional::EXENTO => 'Exento',
        MarinaHumedaCotizacionAdicional::TASA_CERO => 'Tasa cero',
        MarinaHumedaCotizacionAdicional::IVA => 'Iva'
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->mhcservicios = new ArrayCollection();
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
     * Set descuento
     *
     * @param float $descuento
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set dolar
     *
     * @param int $dolar
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;

        return $this;
    }

    /**
     * Get dolar
     *
     * @return int
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * Set iva
     *
     * @param float $iva
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal
     *
     * @param int $subtotal
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal
     *
     * @param int $ivatotal
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setIvatotal($ivatotal)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal
     *
     * @return int
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set descuentototal
     *
     * @param int $descuentototal
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setDescuentototal($descuentototal)
    {
        $this->descuentototal = $descuentototal;

        return $this;
    }

    /**
     * Get descuentototal
     *
     * @return int
     */
    public function getDescuentototal()
    {
        return $this->descuentototal;
    }

    /**
     * Set total
     *
     * @param int $total
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set fecharegistro
     *
     * @param \DateTime $fecharegistro
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setFecharegistro($fecharegistro)
    {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    /**
     * Get fecharegistro
     *
     * @return \DateTime
     */
    public function getFecharegistro()
    {
        return $this->fecharegistro;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set barco
     *
     * @param \AppBundle\Entity\Barco $barco
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function setBarco(\AppBundle\Entity\Barco $barco = null)
    {
        $this->barco = $barco;

        return $this;
    }

    /**
     * Get barco
     *
     * @return \AppBundle\Entity\Barco
     */
    public function getBarco()
    {
        return $this->barco;
    }


    /**
     * Add mhcservicio
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicios
     *
     * @return MarinaHumedaCotizacionAdicional
     */
    public function addMhcservicio(\AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicio)
    {
        $mhcservicio ->setMarinahumedacotizacionadicional($this);
        $this->mhcservicios[] = $mhcservicio;

        return $this;
    }

    /**
     * Remove mhcservicio
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicio
     */
    public function removeMhcservicio(\AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicio)
    {
        $this->mhcservicios->removeElement($mhcservicio);
    }

    /**
     * Get mhcservicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMhcservicios()
    {
        return $this->mhcservicios;
    }


    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public static function getTipoList()
    {
        return self::$tipoList;
    }

    /**
     * @return string
     */
    public function getTipoNombre()
    {
        if (null === $this->tipo) { return null; }
        return self::$tipoList[$this->tipo];
    }
}

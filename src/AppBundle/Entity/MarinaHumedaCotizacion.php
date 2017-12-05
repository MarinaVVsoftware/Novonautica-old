<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MarinaHumedaCotizacion
 *
 * @ORM\Table(name="marina_humeda_cotizacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaCotizacionRepository")
 */
class MarinaHumedaCotizacion
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
     * @Assert\NotBlank(
     *     message="Fecha de llegada no puede quedar vacío"
     * )
     * @Assert\Date()
     *
     * @ORM\Column(name="fecha_llegada", type="datetime")
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Fecha de salida no puede quedar vacío"
     * )
     * @Assert\Date()
     *
     * @ORM\Column(name="fecha_salida", type="datetime")
     */
    private $fechaSalida;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="dolar", type="float", nullable=true)
     */
    private $dolar;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=true)
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="ivatotal", type="float", nullable=true)
     */
    private $ivatotal;

    /**
     * @var float
     *
     * @ORM\Column(name="descuentototal", type="float", nullable=true)
     */
    private $descuentototal;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(name="adeudo", type="float", nullable=true)
     */
    private $adeudo;

    /**
     * @var float
     *
     * @ORM\Column(name="pagado", type="float", nullable=true)
     */
    private $pagado;

    /**
     * @var int
     *
     * @ORM\Column(name="estatuspago", type="smallint", nullable=true)
     */
    private $estatuspago;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text", nullable=true)
     */
    private $mensaje;


    /**
     * @var int
     *
     * @ORM\Column(name="validanovo", type="smallint")
     */
    private $validanovo;

    /**
     * @var int
     *
     * @ORM\Column(name="validacliente", type="smallint")
     */
    private $validacliente;

    /**
     * @var string
     *
     * @ORM\Column(name="notasnovo", type="text", nullable=true)
     */
    private $notasnovo;

    /**
     * @var string
     *
     * @ORM\Column(name="notascliente", type="text", nullable=true)
     */
    private $notascliente;

    /**
     * @var string
     *
     * @ORM\Column(name="nombrevalidanovo", type="string", length=255, nullable=true)
     */
    private $nombrevalidanovo;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @var int
     *
     * @ORM\Column(name="folio", type="integer", length=255)
     */
    private $folio;

    /**
     * @var int
     *
     * @ORM\Column(name="foliorecotiza", type="integer", length=255)
     */
    private $foliorecotiza;

    /**
     * @var string
     *
     * @ORM\Column(name="tokenacepta", type="string", length=110, nullable=true)
     */
    private $tokenacepta;

    /**
     * @var string
     *
     * @ORM\Column(name="tokenrechaza", type="string", length=110, nullable=true)
     */
    private $tokenrechaza;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Slip", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idslip", referencedColumnName="id",onDelete="CASCADE")
     */
    private $slip;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizaServicios", mappedBy="marinahumedacotizacion",cascade={"persist"})
     */
    private $mhcservicios;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pago", mappedBy="mhcotizacion",cascade={"persist"})
     */
    private $pagos;

    public function __construct() {
        $this->mhcservicios = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->folio.' '.$this->foliorecotiza;
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
     * Set fechaLlegada
     *
     * @param \DateTime $fechaLlegada
     *
     * @return MarinaHumedaCotizacion
     */
    public function setFechaLlegada($fechaLlegada)
    {
        $this->fechaLlegada = $fechaLlegada;

        return $this;
    }

    /**
     * Get fechaLlegada
     *
     * @return \DateTime
     */
    public function getFechaLlegada()
    {
        return $this->fechaLlegada;
    }

    /**
     * Set fechaSalida
     *
     * @param \DateTime $fechaSalida
     *
     * @return MarinaHumedaCotizacion
     */
    public function setFechaSalida($fechaSalida)
    {
        $this->fechaSalida = $fechaSalida;

        return $this;
    }

    /**
     * Get fechaSalida
     *
     * @return \DateTime
     */
    public function getFechaSalida()
    {
        return $this->fechaSalida;
    }

    /**
     * Set descuento
     *
     * @param float $descuento
     *
     * @return MarinaHumedaCotizacion
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
     * @param float $dolar
     *
     * @return MarinaHumedaCotizacion
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;

        return $this;
    }

    /**
     * Get dolar
     *
     * @return float
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
     * @return MarinaHumedaCotizacion
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
     * @param float $subtotal
     *
     * @return MarinaHumedaCotizacion
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal
     *
     * @param float $ivatotal
     *
     * @return MarinaHumedaCotizacion
     */
    public function setIvatotal($ivatotal)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal
     *
     * @return float
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set descuentototal
     *
     * @param float $descuentototal
     *
     * @return MarinaHumedaCotizacion
     */
    public function setDescuentototal($descuentototal)
    {
        $this->descuentototal = $descuentototal;

        return $this;
    }

    /**
     * Get descuentototal
     *
     * @return float
     */
    public function getDescuentototal()
    {
        return $this->descuentototal;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return MarinaHumedaCotizacion
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set validanovo
     *
     * @param integer $validanovo
     *
     * @return MarinaHumedaCotizacion
     */
    public function setValidanovo($validanovo)
    {
        $this->validanovo = $validanovo;

        return $this;
    }

    /**
     * Get validanovo
     *
     * @return int
     */
    public function getValidanovo()
    {
        return $this->validanovo;
    }

    /**
     * Set validacliente
     *
     * @param integer $validacliente
     *
     * @return MarinaHumedaCotizacion
     */
    public function setValidacliente($validacliente)
    {
        $this->validacliente = $validacliente;

        return $this;
    }

    /**
     * Get validacliente
     *
     * @return int
     */
    public function getValidacliente()
    {
        return $this->validacliente;
    }

    /**
     * Set notasnovo
     *
     * @param string $notasnovo
     *
     * @return MarinaHumedaCotizacion
     */
    public function setNotasnovo($notasnovo)
    {
        $this->notasnovo = $notasnovo;

        return $this;
    }

    /**
     * Get notasnovo
     *
     * @return string
     */
    public function getNotasnovo()
    {
        return $this->notasnovo;
    }

    /**
     * @param string $nombrevalidanovo
     */
    public function setNombrevalidanovo($nombrevalidanovo)
    {
        $this->nombrevalidanovo = $nombrevalidanovo;
    }

    /**
     * @return string
     */
    public function getNombrevalidanovo()
    {
        return $this->nombrevalidanovo;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return MarinaHumedaCotizacion
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * set folio
     *
     * @param integer $folio
     *
     * @return MarinaHumedaCotizacion
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio
     *
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * set foliorecotiza
     *
     * @param integer $foliorecotiza
     *
     * @return MarinaHumedaCotizacion
     */
    public function setFoliorecotiza($foliorecotiza)
    {
        $this->foliorecotiza = $foliorecotiza;

        return $this;
    }

    /**
     * Get foliorecotiza
     *
     * @return int
     */
    public function getFoliorecotiza()
    {
        return $this->foliorecotiza;
    }

    /**
     * Set notascliente
     *
     * @param string $notascliente
     *
     * @return MarinaHumedaCotizacion
     */
    public function setNotascliente($notascliente)
    {
        $this->notascliente = $notascliente;

        return $this;
    }

    /**
     * Get notascliente
     *
     * @return string
     */
    public function getNotascliente()
    {
        return $this->notascliente;
    }

    /**
     * Set tokenacepta
     *
     * @param string $tokenacepta
     *
     * @return MarinaHumedaCotizacion
     */
    public function setTokenacepta($tokenacepta)
    {
        $this->tokenacepta = $tokenacepta;

        return $this;
    }

    /**
     * Get tokenacepta
     *
     * @return string
     *
     */
    public function getTokenacepta()
    {
        return $this->tokenacepta;
    }

    /**
     * Set tokenrechaza
     *
     * @param string $tokenrechaza
     *
     * @return MarinaHumedaCotizacion
     */
    public function setTokenrechaza($tokenrechaza)
    {
        $this->tokenrechaza = $tokenrechaza;

        return $this;
    }

    /**
     * Get tokenrechaza
     *
     * @return string
     *
     */
    public function getTokenrechaza()
    {
        return $this->tokenrechaza;
    }

    /**
     * Set fecharegistro
     *
     * @param \DateTime $fecharegistro
     *
     * @return MarinaHumedaCotizacion
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
     * @return MarinaHumedaCotizacion
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
     * @return MarinaHumedaCotizacion
     */
    public function setBarco(\AppBundle\Entity\Barco $barco= null)
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
     * Add marinahumedacotizaservicios
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizaServicios $marinahumedacotizaservicios
     *
     * @return MarinaHumedaCotizacion
     */
    public function addMarinaHumedaCotizaServicios(\AppBundle\Entity\MarinaHumedaCotizaServicios $marinahumedacotizaservicios)
    {
        $marinahumedacotizaservicios->setMarinaHumedaCotizacion($this);
        $this->mhcservicios[] = $marinahumedacotizaservicios;
        return $this;
    }

    /**
     * Remove marinahumedacotizaservicios
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizaServicios $marinahumedacotizaservicios
     */
    public function removeMarinaHumedaCotizaServicios(\AppBundle\Entity\MarinaHumedaCotizaServicios $marinahumedacotizaservicios)
    {
        $this->mhcservicios->removeElement($marinahumedacotizaservicios);
    }

    /**
     * Get mhcservicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMHCservicios()
    {
        return $this->mhcservicios;
    }

    /**
     * Add mhcservicio
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicio
     *
     * @return MarinaHumedaCotizacion
     */
    public function addMhcservicio(\AppBundle\Entity\MarinaHumedaCotizaServicios $mhcservicio)
    {
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
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * @param string $mensaje
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }



    /**
     * Set slip
     *
     * @param \AppBundle\Entity\Slip $slip
     *
     * @return MarinaHumedaCotizacion
     */
    public function setSlip(\AppBundle\Entity\Slip $slip = null)
    {
        $this->slip = $slip;

        return $this;
    }

    /**
     * Get slip
     *
     * @return \AppBundle\Entity\Slip
     */
    public function getSlip()
    {
        return $this->slip;
    }

    /**
     * @return float
     */
    public function getAdeudo()
    {
        return $this->adeudo;
    }

    /**
     * @param float $adeudo
     */
    public function setAdeudo($adeudo)
    {
        $this->adeudo = $adeudo;
    }

    /**
     * Add pago
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return MarinaHumedaCotizacion
     */
    public function addPago(\AppBundle\Entity\Pago $pago)
    {
        $pago->setMhcotizacion($this);
        $this->pagos[] = $pago;

        return $this;
    }

    /**
     * Remove pago
     *
     * @param \AppBundle\Entity\Pago $pago
     */
    public function removePago(\AppBundle\Entity\Pago $pago)
    {
        $this->pagos->removeElement($pago);
    }

    /**
     * Get pagos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagos()
    {
        return $this->pagos;
    }

    /**
     * @return int
     */
    public function getEstatuspago()
    {
        return $this->estatuspago;
    }

    /**
     * @param int $estatuspago
     */
    public function setEstatuspago($estatuspago)
    {
        $this->estatuspago = $estatuspago;
    }

    /**
     * @return float
     */
    public function getPagado()
    {
        return $this->pagado;
    }

    /**
     * @param float $pagado
     */
    public function setPagado($pagado)
    {
        $this->pagado = $pagado;
    }
}

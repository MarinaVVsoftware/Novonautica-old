<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
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
     *
     * @Groups({"facturacion", "currentOcupation"})
     */
    private $id;

    /**
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="fecha_llegada", type="datetime", nullable=true)
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     * @Assert\Date()
     *
     * @ORM\Column(name="fecha_salida", type="datetime", nullable=true)
     */
    private $fechaSalida;

    /**
     * @var int
     *
     * @ORM\Column(name="diasEstadia", type="integer", nullable=true)
     */
    private $diasEstadia;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @Groups({"facturacion"})
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
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="ivatotal", type="bigint", nullable=true)
     */
    private $ivatotal;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="descuentototal", type="bigint", nullable=true)
     */
    private $descuentototal;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="adeudo", type="bigint", nullable=true)
     */
    private $adeudo;

    /**
     * @var integer
     *
     * @ORM\Column(name="pagado", type="bigint", nullable=true)
     */
    private $pagado;

    /**
     * @var int 0 = No ha pagado, 1 = Tiene adeudos, 2 = Ya pago
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
     * @var int Estatus: 0 Pendiente, 1 Rechazado, 2 Aceptado
     *
     * @ORM\Column(name="validanovo", type="smallint")
     */
    private $validanovo;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_valida_novo", type="datetime_immutable", nullable=true)
     */
    private $registroValidaNovo;

    /**
     * @var int Estatus: 0 Pendiente, 1 Rechazado, 2 Aceptado
     *
     * @ORM\Column(name="validacliente", type="smallint")
     */
    private $validacliente;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_valida_cliente", type="datetime_immutable", nullable=true)
     */
    private $registroValidaCliente;

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
     * @Groups({"facturacion", "currentOcupation"})
     *
     * @ORM\Column(name="folio", type="integer", length=255)
     */
    private $folio;

    /**
     * @var int
     *
     * @Groups({"facturacion", "currentOcupation"})
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
     * @var string
     *
     * @ORM\Column(name="metodopago", type="string", length=100, nullable=true)
     */
    private $metodopago;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoseguimiento", type="string", length=255, nullable=true)
     */
    private $codigoseguimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharealpago", type="datetime", nullable=true)
     */
    private $fecharespuesta;

    /**
     * @var bool
     *
     * @ORM\Column(name="notificar_cliente", type="boolean")
     */
    private $notificarCliente;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_pago_completado", type="datetime_immutable", nullable=true)
     */
    private $registroPagoCompletado;

    /**
     * @Groups({"currentOcupation"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     * @Groups({"currentOcupation"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Slip", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idslip", referencedColumnName="id", onDelete="CASCADE")
     */
    private $slip;

    /**
     * @Groups({"facturacion"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizaServicios", mappedBy="marinahumedacotizacion",cascade={"persist"})
     */
    private $mhcservicios;

    /**
     * @Groups({"facturacion"})
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pago", mappedBy="mhcotizacion",cascade={"persist","remove"})
     */
    private $pagos;

    /**
     * @ORM\OneToOne(targetEntity="SlipMovimiento", mappedBy="marinahumedacotizacion")
     */
    private $slipmovimiento;

    /**
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CotizacionNota", mappedBy="mhcotizacion",cascade={"persist","remove"})
     */
    private $cotizacionnotas;

    public function __construct()
    {
        $this->mhcservicios = new ArrayCollection();
        $this->pagos = new ArrayCollection();
        $this->notificarCliente = true;
        $this->cotizacionnotas = new ArrayCollection();
        $this->registroValidaNovo = null;
        $this->registroValidaCliente = null;
        $this->registroPagoCompletado = null;
    }

    public function __toString()
    {
        $f = $this->folio . ($this->foliorecotiza ? '-'.$this->foliorecotiza : '');
        return 'Folio: ' . $f . ' - Barco: ' . $this->getBarco() . ' - Eslora: ' . $this->getBarco()->getEslora();
    }

    public function __clone()
    {
        $this->id = null;
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
     * @param int $subtotal
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
     * @return int
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
     * Set registroValidaNovo.
     *
     * @param \DateTimeImmutable|null $registroValidaNovo
     *
     * @return MarinaHumedaCotizacion
     */
    public function setRegistroValidaNovo($registroValidaNovo = null)
    {
        $this->registroValidaNovo = $registroValidaNovo;

        return $this;
    }

    /**
     * Get registroValidaNovo.
     *
     * @return \DateTimeImmutable|null
     */
    public function getRegistroValidaNovo()
    {
        return $this->registroValidaNovo;
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
     * Set registroValidaCliente.
     *
     * @param \DateTimeImmutable|null $registroValidaCliente
     *
     * @return MarinaHumedaCotizacion
     */
    public function setRegistroValidaCliente($registroValidaCliente = null)
    {
        $this->registroValidaCliente = $registroValidaCliente;

        return $this;
    }

    /**
     * Get registroValidaCliente.
     *
     * @return \DateTimeImmutable|null
     */
    public function getRegistroValidaCliente()
    {
        return $this->registroValidaCliente;
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

    /**
     * @return string
     */
    public function getMetodopago()
    {
        return $this->metodopago;
    }

    /**
     * @param string $metodopago
     */
    public function setMetodopago($metodopago)
    {
        $this->metodopago = $metodopago;
    }

    /**
     * @return string
     */
    public function getCodigoseguimiento()
    {
        return $this->codigoseguimiento;
    }

    /**
     * @param string $codigoseguimiento
     */
    public function setCodigoseguimiento($codigoseguimiento)
    {
        $this->codigoseguimiento = $codigoseguimiento;
    }

    /**
     * @return \DateTime
     */
    public function getFecharespuesta()
    {
        return $this->fecharespuesta;
    }

    /**
     * @param \DateTime $fecharespuesta
     */
    public function setFecharespuesta($fecharespuesta)
    {
        $this->fecharespuesta = $fecharespuesta;
    }

    /**
     * Set slipmovimiento
     *
     * @param \AppBundle\Entity\SlipMovimiento $slipmovimiento
     *
     * @return MarinaHumedaCotizacion
     */
    public function setSlipmovimiento(\AppBundle\Entity\SlipMovimiento $slipmovimiento = null)
    {
        $this->slipmovimiento = $slipmovimiento;

        return $this;
    }

    /**
     * Get slipmovimiento
     *
     * @return \AppBundle\Entity\SlipMovimiento
     */
    public function getSlipmovimiento()
    {
        return $this->slipmovimiento;
    }

    /**
     * @return bool
     */
    public function isNotificarCliente()
    {
        return $this->notificarCliente;
    }

    /**
     * @param bool $notificarCliente
     */
    public function setNotificarCliente($notificarCliente)
    {
        $this->notificarCliente = $notificarCliente;
    }

    /**
     * Get notificarCliente.
     *
     * @return bool
     */
    public function getNotificarCliente()
    {
        return $this->notificarCliente;
    }

    /**
     * @return int
     */
    public function getDiasEstadia()
    {
        return $this->diasEstadia;
    }

    /**
     * @param int $diasEstadia
     */
    public function setDiasEstadia($diasEstadia)
    {
        $this->diasEstadia = $diasEstadia;
    }


    /**
     * Add cotizacionnota.
     *
     * @param \AppBundle\Entity\CotizacionNota $cotizacionnota
     *
     * @return MarinaHumedaCotizacion
     */
    public function addCotizacionnota(\AppBundle\Entity\CotizacionNota $cotizacionnota)
    {
        $cotizacionnota->setMhcotizacion($this);
        $this->cotizacionnotas[] = $cotizacionnota;

        return $this;
    }

    /**
     * Remove cotizacionnota.
     *
     * @param \AppBundle\Entity\CotizacionNota $cotizacionnota
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCotizacionnota(\AppBundle\Entity\CotizacionNota $cotizacionnota)
    {
        return $this->cotizacionnotas->removeElement($cotizacionnota);
    }

    /**
     * Get cotizacionnotas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCotizacionnotas()
    {
        return $this->cotizacionnotas;
    }


    /**
     * Set registroPagoCompletado.
     *
     * @param \DateTimeImmutable|null $registroPagoCompletado
     *
     * @return MarinaHumedaCotizacion
     */
    public function setRegistroPagoCompletado($registroPagoCompletado = null)
    {
        $this->registroPagoCompletado = $registroPagoCompletado;

        return $this;
    }

    /**
     * Get registroPagoCompletado.
     *
     * @return \DateTimeImmutable|null
     */
    public function getRegistroPagoCompletado()
    {
        return $this->registroPagoCompletado;
    }
}
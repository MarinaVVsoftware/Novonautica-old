<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AstilleroCotizacion
 *
 * @ORM\Table(name="astillero_cotizacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AstilleroCotizacionRepository")
 */
class AstilleroCotizacion
{
    /**
     * @var int
     *
     * @Groups({"facturacion", "AstilleroReporte"})
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
     * @ORM\Column(name="fechaLlegada", type="datetime")
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Fecha de salida no puede quedar vacío"
     * )
     * @Assert\Date()
     *
     * @ORM\Column(name="fechaSalida", type="datetime")
     */
    private $fechaSalida;

    /**
     * @var int
     *
     * @Assert\NotBlank()
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
     * @var integer
     *
     * @Groups({"facturacion", "AstilleroReporte"})
     *
     * @ORM\Column(name="dolar", type="bigint", nullable=true)
     */
    private $dolar;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=true, precision=19, scale=4)
     */
    private $iva;

    /**
     * @var decimal
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true, precision=19, scale=4)
     */
    private $subtotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="descuentototal", type="bigint", nullable=true)
     */
    private $descuentototal;

    /**
     * @var decimal
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="ivatotal", type="decimal", nullable=true, precision=19, scale=4)
     */
    private $ivatotal;

    /**
     * @var decimal
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="total", type="decimal", nullable=true,  precision=19, scale=4)
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     * @Groups({"AstilleroReporte"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="astillerocotizaciones")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     *
     * @Groups({"facturacion"})
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizaServicio", mappedBy="astillerocotizacion",cascade={"persist"})
     */
    private $acservicios;

    /**
     *
     * @Groups({"facturacion"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pago", mappedBy="acotizacion",cascade={"persist","remove"})
     */
    private $pagos;

    /**
     * @var int
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
     * 0 = pendiente, 1 = rechazado, 2 = aceptado
     *
     * @var int
     *
     * @ORM\Column(name="validacliente", type="smallint")
     */
    private $validacliente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="limite_valida_cliente", type="datetime", nullable=true)
     */
    private $limiteValidaCliente;

    /**
     * Se refiere a un cliente que haya aceptado la cotizacion o a un usuario de novonautica
     *
     * @var string
     *
     * @ORM\Column(name="quien_acepto", type="string", length=100, nullable=true)
     */
    private $quienAcepto;

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
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="folio", type="integer", length=255)
     */
    private $folio;

    /**
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="foliorecotiza", type="integer", length=255)
     */
    private $foliorecotiza;

    /**
     * Se refiere a que usuario de novonautica valido la cotizacion
     *
     * @var string
     *
     * @ORM\Column(name="nombrevalidanovo", type="string", length=255, nullable=true)
     */
    private $nombrevalidanovo;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=110, nullable=true)
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @var bool
     *
     * @ORM\Column(name="borrador", type="boolean")
     */
    private $borrador;

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
     * @var bool
     *
     * @ORM\Column(name="requerir_factura", type="boolean")
     */
    private $requerirFactura;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_pago_completado", type="datetime_immutable", nullable=true)
     */
    private $registroPagoCompletado;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\OrdenDeTrabajo", mappedBy="astilleroCotizacion")
     */
    private $odt;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="astilleroCotizaciones")
     */
    private $cliente;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

    /**
     * @var Facturacion
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion", inversedBy="cotizacionAstillero")
     */
    private $factura;

    public function __construct() {
        $this->acservicios = new ArrayCollection();
        $this->pagos = new ArrayCollection();
        $this->notificarCliente = true;
        $this->registroValidaNovo = null;
        $this->registroValidaCliente = null;
        $this->registroPagoCompletado = null;
        $this->validanovo = 0;
        $this->validacliente = 0;
        $this->foliorecotiza = 0;
        $this->descuento = 0;
    }

    public function __toString()
    {
        $folio = !$this->foliorecotiza ? $this->folio : $this->folio . '-' . $this->foliorecotiza;
        return 'Folio: ' . $folio . ' Barco:  ' . $this->getBarco();
    }

    public function getFolioString()
    {
        return !$this->foliorecotiza ? $this->folio : $this->folio . '-' . $this->foliorecotiza;
    }

    public function getKind()
    {
        return 'Astillero';
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
     * @return AstilleroCotizacion
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
     * @return AstilleroCotizacion
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
     * Set diasEstadia
     *
     * @param integer $diasEstadia
     *
     * @return AstilleroCotizacion
     */
    public function setDiasEstadia($diasEstadia)
    {
        $this->diasEstadia = $diasEstadia;

        return $this;
    }

    /**
     * Get diasEstadia
     *
     * @return int
     */
    public function getDiasEstadia()
    {
        return $this->diasEstadia;
    }

    /**
     * Set dolar
     *
     * @param int $dolar
     *
     * @return AstilleroCotizacion
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
     * @return AstilleroCotizacion
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
     * @param decimal $subtotal
     *
     * @return AstilleroCotizacion
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return decimal
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal
     *
     * @param decimal $ivatotal
     *
     * @return AstilleroCotizacion
     */
    public function setIvatotal($ivatotal)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal
     *
     * @return decimal
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total
     *
     * @param decimal $total
     *
     * @return AstilleroCotizacion
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return decimal
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
     * @return AstilleroCotizacion
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
     * Set barco
     *
     * @param Barco $barco
     *
     * @return AstilleroCotizacion
     */
    public function setBarco(Barco $barco = null)
    {
        $this->barco = $barco;

        return $this;
    }

    /**
     * Get barco
     *
     * @return Barco
     */
    public function getBarco()
    {
        return $this->barco;
    }

    /**
     * Add acservicio
     *
     * @param AstilleroCotizaServicio $acservicio
     *
     * @return AstilleroCotizacion
     */
    public function addAcservicio(AstilleroCotizaServicio $acservicio)
    {
        $acservicio ->setAstillerocotizacion($this);
        $this->acservicios[] = $acservicio;

        return $this;
    }

    /**
     * Remove acservicio
     *
     * @param AstilleroCotizaServicio $acservicio
     */
    public function removeAcservicio(AstilleroCotizaServicio $acservicio)
    {
        $this->acservicios->removeElement($acservicio);
    }

    /**
     * Get acservicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcservicios()
    {
        return $this->acservicios;
    }

    /**
     * @return int
     */
    public function getValidanovo()
    {
        return $this->validanovo;
    }

    /**
     * @param int $validanovo
     */
    public function setValidanovo($validanovo)
    {
        $this->validanovo = $validanovo;
    }

    /**
     * @return int
     */
    public function getValidacliente()
    {
        return $this->validacliente;
    }

    /**
     * @param int $validacliente
     */
    public function setValidacliente($validacliente)
    {
        $this->validacliente = $validacliente;
    }

    /**
     * @return string
     */
    public function getQuienAcepto()
    {
        return $this->quienAcepto;
    }

    /**
     * @param string $quienAcepto
     */
    public function setQuienAcepto($quienAcepto)
    {
        $this->quienAcepto = $quienAcepto;
    }

    /**
     * @return bool
     */
    public function isEstatus()
    {
        return $this->estatus;
    }

    /**
     * @param bool $estatus
     *
     * @return AstilleroCotizacion
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
        return $this;
    }

    /**
     * Get estatus
     *
     * @return boolean
     */
    public function getEstatus()
    {
        return $this->estatus;
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
     * @return AstilleroCotizacion
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
        return $this;
    }

    /**
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * @param int $folio
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;
    }

    /**
     * @return int
     */
    public function getFoliorecotiza()
    {
        return $this->foliorecotiza;
    }

    /**
     * @param int $foliorecotiza
     *
     * @return AstilleroCotizacion
     */
    public function setFoliorecotiza($foliorecotiza)
    {
        $this->foliorecotiza = $foliorecotiza;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombrevalidanovo()
    {
        return $this->nombrevalidanovo;
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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
    public function getNotasnovo()
    {
        return $this->notasnovo;
    }

    /**
     * @param string $notasnovo
     */
    public function setNotasnovo($notasnovo)
    {
        $this->notasnovo = $notasnovo;
    }

    /**
     * @return string
     */
    public function getNotascliente()
    {
        return $this->notascliente;
    }

    /**
     * @param string $notascliente
     */
    public function setNotascliente($notascliente)
    {
        $this->notascliente = $notascliente;
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
     * @return int
     */
    public function getAdeudo()
    {
        return $this->adeudo;
    }

    /**
     * @param int $adeudo
     */
    public function setAdeudo($adeudo)
    {
        $this->adeudo = $adeudo;
    }

    /**
     * @return int
     */
    public function getPagado()
    {
        return $this->pagado;
    }

    /**
     * @param int $pagado
     */
    public function setPagado($pagado)
    {
        $this->pagado = $pagado;
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
     * Add pago
     *
     * @param Pago $pago
     *
     * @return AstilleroCotizacion
     */
    public function addPago(Pago $pago)
    {
        $pago->setAcotizacion($this);
        $this->pagos[] = $pago;

        return $this;
    }

    /**
     * Remove pago
     *
     * @param Pago $pago
     */
    public function removePago(Pago $pago)
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
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * @param float $descuento
     * @return AstilleroCotizacion
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
        return $this;
    }

    /**
     * Set odt
     *
     * @param \AppBundle\Entity\OrdenDeTrabajo $odt
     *
     * @return AstilleroCotizacion
     */
    public function setOdt(\AppBundle\Entity\OrdenDeTrabajo $odt = null)
    {
        $this->odt = $odt;

        return $this;
    }

    /**
     * Get odt
     *
     * @return \AppBundle\Entity\OrdenDeTrabajo
     */
    public function getOdt()
    {
        return $this->odt;
    }

    /**
     * Set cliente
     *
     * @param Cliente $cliente
     *
     * @return AstilleroCotizacion
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set registroValidaNovo.
     *
     * @param \DateTimeImmutable|null $registroValidaNovo
     *
     * @return AstilleroCotizacion
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
     * Set registroValidaCliente.
     *
     * @param \DateTimeImmutable|null $registroValidaCliente
     *
     * @return AstilleroCotizacion
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
     * @return bool
     */
    public function isNotificarCliente()
    {
        return $this->notificarCliente;
    }

    /**
     * Set notificarCliente.
     *
     * @param bool $notificarCliente
     *
     * @return AstilleroCotizacion
     */
    public function setNotificarCliente($notificarCliente)
    {
        $this->notificarCliente = $notificarCliente;

        return $this;
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
     * Set registroPagoCompletado.
     *
     * @param \DateTimeImmutable|null $registroPagoCompletado
     *
     * @return AstilleroCotizacion
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

    /**
     * @return Usuario
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * @param Usuario $creador
     */
    public function setCreador($creador)
    {
        $this->creador = $creador;
    }

    /**
     * Set borrador.
     *
     * @param bool $borrador
     *
     * @return AstilleroCotizacion
     */
    public function setBorrador($borrador)
    {
        $this->borrador = $borrador;

        return $this;
    }

    /**
     * Get borrador.
     *
     * @return bool
     */
    public function getBorrador()
    {
        return $this->borrador;
    }

    /**
     * Set descuentototal.
     *
     * @param int|null $descuentototal
     *
     * @return AstilleroCotizacion
     */
    public function setDescuentototal($descuentototal = null)
    {
        $this->descuentototal = $descuentototal;

        return $this;
    }

    /**
     * Get descuentototal.
     *
     * @return int|null
     */
    public function getDescuentototal()
    {
        return $this->descuentototal;
    }

    /**
     * Set limiteValidaCliente.
     *
     * @param \DateTime|null $limiteValidaCliente
     *
     * @return AstilleroCotizacion
     */
    public function setLimiteValidaCliente($limiteValidaCliente = null)
    {
        $this->limiteValidaCliente = $limiteValidaCliente;

        return $this;
    }

    /**
     * Get limiteValidaCliente.
     *
     * @return \DateTime|null
     */
    public function getLimiteValidaCliente()
    {
        return $this->limiteValidaCliente;
    }

    /**
     * @return Facturacion
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * @param Facturacion $factura
     */
    public function setFactura(Facturacion $factura = null)
    {
        $this->factura = $factura;
    }

    /**
     * @param $requerirFactura
     * @return AstilleroCotizacion
     */
    public function setRequerirFactura($requerirFactura)
    {
        $this->requerirFactura = $requerirFactura;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequerirFactura()
    {
        return $this->requerirFactura;
    }
}

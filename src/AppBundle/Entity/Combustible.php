<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Combustible
 *
 * @ORM\Table(name="combustible")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CombustibleRepository")
 */
class Combustible
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
     * @var int
     *
     * @ORM\Column(name="folio", type="integer")
     */
    private $folio;

    /**
     * @var int|null
     *
     * @ORM\Column(name="foliorecotiza", type="integer", nullable=true)
     */
    private $foliorecotiza;

    /**
     * @var int
     *
     * @ORM\Column(name="dolar", type="bigint")
     */
    private $dolar;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var float|null
     *
     * @ORM\Column(name="cuota_iesps", type="float", nullable=true)
     */
    private $cuotaIesps;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="precio_venta", type="bigint")
     */
    private $precioVenta;

    /**
     * @var int
     *
     * @ORM\Column(name="precio_sin_iesps", type="bigint")
     */
    private $precioSinIesps;

    /**
     * @var int
     *
     * @ORM\Column(name="precio_sin_iva_iesps", type="bigint")
     */
    private $precioSinIvaIesps;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="iva_total", type="bigint", nullable=true)
     */
    private $ivaTotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="iesps_total", type="bigint", nullable=true)
     */
    private $iespsTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="total_sin_iesps", type="bigint")
     */
    private $totalSinIesps;

    /**
     * @var int
     *
     * @ORM\Column(name="total_sin_iva_iesps", type="bigint")
     */
    private $totalSinIvaIesps;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * Quien creo esta cotizacion
     *
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

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
     * @var bool
     *
     * @ORM\Column(name="notificar_cliente", type="boolean")
     */
    private $notificarCliente;

    /**
     * @var int Estatus: 0 Pendiente, 1 Rechazado, 2 Aceptado
     *
     * @ORM\Column(name="validanovo", type="smallint")
     */
    private $validanovo;

    /**
     * @var int Estatus: 0 Pendiente, 1 Rechazado, 2 Aceptado
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
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_valida_novo", type="datetime_immutable", nullable=true)
     */
    private $registroValidaNovo;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_valida_cliente", type="datetime_immutable", nullable=true)
     */
    private $registroValidaCliente;

    /**
     * @var string
     *
     * @ORM\Column(name="nombrevalidanovo", type="string", length=255, nullable=true)
     */
    private $nombrevalidanovo;

    /**
     * @var string
     *
     * @ORM\Column(name="quien_acepto", type="string", length=100, nullable=true)
     */
    private $quienAcepto;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=110, nullable=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="combustibles")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="combustibles")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Combustible\Catalogo")
     * @ORM\JoinColumn(name="idtipo", referencedColumnName="id")
     */
    private $tipo;

    /**
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pago", mappedBy="combustible",cascade={"persist","remove"})
     */
    private $pagos;

    /**
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CotizacionNota", mappedBy="combustible",cascade={"persist","remove"})
     */
    private $cotizacionnotas;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_pago_completado", type="datetime_immutable", nullable=true)
     */
    private $registroPagoCompletado;

    public function __construct()
    {
        $this->cantidad = 0;
        $this->precioVenta = 0;
        $this->precioSinIesps = 0;
        $this->precioSinIvaIesps = 0;
        $this->cuotaIesps = 0;
        $this->subtotal = 0;
        $this->ivaTotal = 0;
        $this->iespsTotal = 0;
        $this->totalSinIesps = 0;
        $this->totalSinIvaIesps = 0;
        $this->total = 0;
        $this->notificarCliente = true;
        $this->validanovo = 0;
        $this->validacliente = 0;
        $this->estatus = true;
        $this->pagos = new ArrayCollection();
        $this->cotizacionnotas = new ArrayCollection();
    }

    public function __clone()
    {
        $this->setId(null);
        $this->setValidanovo(0);
        $this->setValidacliente(0);
        $this->setNotasnovo(null);
        $this->setNotascliente(null);
        $this->setRegistroValidaNovo(null);
        $this->setToken(null);
    }

    public function getFolioCompleto(){
        return $this->getFoliorecotiza() === 0 ? $this->getFolio() : $this->getFolio().'-'.$this->getFoliorecotiza();
    }

    /**
     * Set folio.
     *
     * @param int $id
     *
     * @return Combustible
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set folio.
     *
     * @param int $folio
     *
     * @return Combustible
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
     * Set foliorecotiza.
     *
     * @param int|null $foliorecotiza
     *
     * @return Combustible
     */
    public function setFoliorecotiza($foliorecotiza = null)
    {
        $this->foliorecotiza = $foliorecotiza;

        return $this;
    }

    /**
     * Get foliorecotiza.
     *
     * @return int|null
     */
    public function getFoliorecotiza()
    {
        return $this->foliorecotiza;
    }

    /**
     * Set dolar.
     *
     * @param float $dolar
     *
     * @return Combustible
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;

        return $this;
    }

    /**
     * Get dolar.
     *
     * @return float
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * Set iva.
     *
     * @param float $iva
     *
     * @return Combustible
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return float
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
     * @return Combustible
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
     * Set total.
     *
     * @param int|null $total
     *
     * @return Combustible
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
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return Combustible
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
     * Set cuotaIesps.
     *
     * @param float|null $cuotaIesps
     *
     * @return Combustible
     */
    public function setCuotaIesps($cuotaIesps = null)
    {
        $this->cuotaIesps = $cuotaIesps;

        return $this;
    }

    /**
     * Get cuotaIesps.
     *
     * @return float|null
     */
    public function getCuotaIesps()
    {
        return $this->cuotaIesps;
    }

    /**
     * Set ivaTotal.
     *
     * @param int|null $ivaTotal
     *
     * @return Combustible
     */
    public function setIvaTotal($ivaTotal = null)
    {
        $this->ivaTotal = $ivaTotal;

        return $this;
    }

    /**
     * Get ivaTotal.
     *
     * @return int|null
     */
    public function getIvaTotal()
    {
        return $this->ivaTotal;
    }

    /**
     * Set iespsTotal.
     *
     * @param int|null $iespsTotal
     *
     * @return Combustible
     */
    public function setIespsTotal($iespsTotal = null)
    {
        $this->iespsTotal = $iespsTotal;

        return $this;
    }

    /**
     * Get iespsTotal.
     *
     * @return int|null
     */
    public function getIespsTotal()
    {
        return $this->iespsTotal;
    }

    /**
     * Set estatus.
     *
     * @param bool $estatus
     *
     * @return Combustible
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus.
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Combustible
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
     * Set precioVenta.
     *
     * @param int $precioVenta
     *
     * @return Combustible
     */
    public function setPrecioVenta($precioVenta)
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    /**
     * Get precioVenta.
     *
     * @return int
     */
    public function getPrecioVenta()
    {
        return $this->precioVenta;
    }

    /**
     * Set pagado.
     *
     * @param int|null $pagado
     *
     * @return Combustible
     */
    public function setPagado($pagado = null)
    {
        $this->pagado = $pagado;

        return $this;
    }

    /**
     * Get pagado.
     *
     * @return int|null
     */
    public function getPagado()
    {
        return $this->pagado;
    }

    /**
     * Set estatuspago.
     *
     * @param int|null $estatuspago
     *
     * @return Combustible
     */
    public function setEstatuspago($estatuspago = null)
    {
        $this->estatuspago = $estatuspago;

        return $this;
    }

    /**
     * Get estatuspago.
     *
     * @return int|null
     */
    public function getEstatuspago()
    {
        return $this->estatuspago;
    }

    /**
     * Set mensaje.
     *
     * @param string|null $mensaje
     *
     * @return Combustible
     */
    public function setMensaje($mensaje = null)
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje.
     *
     * @return string|null
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set creador.
     *
     * @param \AppBundle\Entity\Usuario|null $creador
     *
     * @return Combustible
     */
    public function setCreador(\AppBundle\Entity\Usuario $creador = null)
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
     * Set cliente.
     *
     * @param \AppBundle\Entity\Cliente|null $cliente
     *
     * @return Combustible
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente.
     *
     * @return \AppBundle\Entity\Cliente|null
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set barco.
     *
     * @param \AppBundle\Entity\Barco|null $barco
     *
     * @return Combustible
     */
    public function setBarco(\AppBundle\Entity\Barco $barco = null)
    {
        $this->barco = $barco;

        return $this;
    }

    /**
     * Get barco.
     *
     * @return \AppBundle\Entity\Barco|null
     */
    public function getBarco()
    {
        return $this->barco;
    }

    /**
     * Set tipo.
     *
     * @param \AppBundle\Entity\Combustible\Catalogo|null $tipo
     *
     * @return Combustible
     */
    public function setTipo(\AppBundle\Entity\Combustible\Catalogo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return \AppBundle\Entity\Combustible\Catalogo|null
     */
    public function getTipo()
    {
        return $this->tipo;
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
     * @return Combustible
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
     * Set totalSinIesps.
     *
     * @param int $totalSinIesps
     *
     * @return Combustible
     */
    public function setTotalSinIesps($totalSinIesps)
    {
        $this->totalSinIesps = $totalSinIesps;

        return $this;
    }

    /**
     * Get totalSinIesps.
     *
     * @return int
     */
    public function getTotalSinIesps()
    {
        return $this->totalSinIesps;
    }

    /**
     * Set totalSinIvaIesps.
     *
     * @param int $totalSinIvaIesps
     *
     * @return Combustible
     */
    public function setTotalSinIvaIesps($totalSinIvaIesps)
    {
        $this->totalSinIvaIesps = $totalSinIvaIesps;

        return $this;
    }

    /**
     * Get totalSinIvaIesps.
     *
     * @return int
     */
    public function getTotalSinIvaIesps()
    {
        return $this->totalSinIvaIesps;
    }

    /**
     * Set precioSinIesps.
     *
     * @param int $precioSinIesps
     *
     * @return Combustible
     */
    public function setPrecioSinIesps($precioSinIesps)
    {
        $this->precioSinIesps = $precioSinIesps;

        return $this;
    }

    /**
     * Get precioSinIesps.
     *
     * @return int
     */
    public function getPrecioSinIesps()
    {
        return $this->precioSinIesps;
    }

    /**
     * Set precioSinIvaIesps.
     *
     * @param int $precioSinIvaIesps
     *
     * @return Combustible
     */
    public function setPrecioSinIvaIesps($precioSinIvaIesps)
    {
        $this->precioSinIvaIesps = $precioSinIvaIesps;

        return $this;
    }

    /**
     * Get precioSinIvaIesps.
     *
     * @return int
     */
    public function getPrecioSinIvaIesps()
    {
        return $this->precioSinIvaIesps;
    }

    /**
     * Set validanovo.
     *
     * @param int $validanovo
     *
     * @return Combustible
     */
    public function setValidanovo($validanovo)
    {
        $this->validanovo = $validanovo;

        return $this;
    }

    /**
     * Get validanovo.
     *
     * @return int
     */
    public function getValidanovo()
    {
        return $this->validanovo;
    }

    /**
     * Set validacliente.
     *
     * @param int $validacliente
     *
     * @return Combustible
     */
    public function setValidacliente($validacliente)
    {
        $this->validacliente = $validacliente;

        return $this;
    }

    /**
     * Get validacliente.
     *
     * @return int
     */
    public function getValidacliente()
    {
        return $this->validacliente;
    }

    /**
     * Set registroValidaNovo.
     *
     * @param datetime_immutable|null $registroValidaNovo
     *
     * @return Combustible
     */
    public function setRegistroValidaNovo($registroValidaNovo = null)
    {
        $this->registroValidaNovo = $registroValidaNovo;

        return $this;
    }

    /**
     * Get registroValidaNovo.
     *
     * @return datetime_immutable|null
     */
    public function getRegistroValidaNovo()
    {
        return $this->registroValidaNovo;
    }

    /**
     * Set registroValidaCliente.
     *
     * @param datetime_immutable|null $registroValidaCliente
     *
     * @return Combustible
     */
    public function setRegistroValidaCliente($registroValidaCliente = null)
    {
        $this->registroValidaCliente = $registroValidaCliente;

        return $this;
    }

    /**
     * Get registroValidaCliente.
     *
     * @return datetime_immutable|null
     */
    public function getRegistroValidaCliente()
    {
        return $this->registroValidaCliente;
    }

    /**
     * Set nombrevalidanovo.
     *
     * @param string|null $nombrevalidanovo
     *
     * @return Combustible
     */
    public function setNombrevalidanovo($nombrevalidanovo = null)
    {
        $this->nombrevalidanovo = $nombrevalidanovo;

        return $this;
    }

    /**
     * Get nombrevalidanovo.
     *
     * @return string|null
     */
    public function getNombrevalidanovo()
    {
        return $this->nombrevalidanovo;
    }

    /**
     * Set quienAcepto.
     *
     * @param string|null $quienAcepto
     *
     * @return Combustible
     */
    public function setQuienAcepto($quienAcepto = null)
    {
        $this->quienAcepto = $quienAcepto;

        return $this;
    }

    /**
     * Get quienAcepto.
     *
     * @return string|null
     */
    public function getQuienAcepto()
    {
        return $this->quienAcepto;
    }

    /**
     * Add pago.
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return Combustible
     */
    public function addPago(\AppBundle\Entity\Pago $pago)
    {
        $pago->setCombustible($this);
        $this->pagos[] = $pago;

        return $this;
    }

    /**
     * Remove pago.
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePago(\AppBundle\Entity\Pago $pago)
    {
        return $this->pagos->removeElement($pago);
    }

    /**
     * Get pagos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagos()
    {
        return $this->pagos;
    }

    /**
     * Add cotizacionnota.
     *
     * @param \AppBundle\Entity\CotizacionNota $cotizacionnota
     *
     * @return Combustible
     */
    public function addCotizacionnota(\AppBundle\Entity\CotizacionNota $cotizacionnota)
    {
        $cotizacionnota->setCombustible($this);
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
     * Set token.
     *
     * @param string|null $token
     *
     * @return Combustible
     */
    public function setToken($token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set notasnovo.
     *
     * @param string|null $notasnovo
     *
     * @return Combustible
     */
    public function setNotasnovo($notasnovo = null)
    {
        $this->notasnovo = $notasnovo;

        return $this;
    }

    /**
     * Get notasnovo.
     *
     * @return string|null
     */
    public function getNotasnovo()
    {
        return $this->notasnovo;
    }

    /**
     * Set notascliente.
     *
     * @param string|null $notascliente
     *
     * @return Combustible
     */
    public function setNotascliente($notascliente = null)
    {
        $this->notascliente = $notascliente;

        return $this;
    }

    /**
     * Get notascliente.
     *
     * @return string|null
     */
    public function getNotascliente()
    {
        return $this->notascliente;
    }

    /**
     * Set registroPagoCompletado.
     *
     * @param datetime_immutable|null $registroPagoCompletado
     *
     * @return Combustible
     */
    public function setRegistroPagoCompletado($registroPagoCompletado = null)
    {
        $this->registroPagoCompletado = $registroPagoCompletado;

        return $this;
    }

    /**
     * Get registroPagoCompletado.
     *
     * @return datetime_immutable|null
     */
    public function getRegistroPagoCompletado()
    {
        return $this->registroPagoCompletado;
    }

    /**
     * Set limiteValidaCliente.
     *
     * @param \DateTime|null $limiteValidaCliente
     *
     * @return Combustible
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
}

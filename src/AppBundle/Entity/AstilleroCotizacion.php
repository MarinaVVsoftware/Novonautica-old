<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(name="diasEstadia", type="integer", nullable=true)
     */
    private $diasEstadia;

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
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;
    /**
     * @var integer
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
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="astillerocotizaciones")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="astillerocotizaciones")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
     */
    private $barco;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizaServicio", mappedBy="astillerocotizacion",cascade={"persist"})
     */
    private $acservicios;

    /**
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
     * @ORM\Column(name="nombrevalidanovo", type="string", length=255, nullable=true)
     */
    private $nombrevalidanovo;

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
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

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



    public function __construct() {
        $this->acservicios = new ArrayCollection();
        $this->pagos = new ArrayCollection();
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
     * @param float $dolar
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
     * @param float $subtotal
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
     * @return float
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total
     *
     * @param float $total
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
     * @return float
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
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return AstilleroCotizacion
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
     * @return AstilleroCotizacion
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
     * Add acservicio
     *
     * @param \AppBundle\Entity\AstilleroCotizaServicio $acservicio
     *
     * @return AstilleroCotizacion
     */
    public function addAcservicio(\AppBundle\Entity\AstilleroCotizaServicio $acservicio)
    {
        $acservicio ->setAstillerocotizacion($this);
        $this->acservicios[] = $acservicio;

        return $this;
    }

    /**
     * Remove acservicio
     *
     * @param \AppBundle\Entity\AstilleroCotizaServicio $acservicio
     */
    public function removeAcservicio(\AppBundle\Entity\AstilleroCotizaServicio $acservicio)
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
     * @return bool
     */
    public function isEstatus()
    {
        return $this->estatus;
    }

    /**
     * @param bool $estatus
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
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
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
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
     */
    public function setFoliorecotiza($foliorecotiza)
    {
        $this->foliorecotiza = $foliorecotiza;
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
    public function getTokenacepta()
    {
        return $this->tokenacepta;
    }

    /**
     * @param string $tokenacepta
     */
    public function setTokenacepta($tokenacepta)
    {
        $this->tokenacepta = $tokenacepta;
    }

    /**
     * @return string
     */
    public function getTokenrechaza()
    {
        return $this->tokenrechaza;
    }

    /**
     * @param string $tokenrechaza
     */
    public function setTokenrechaza($tokenrechaza)
    {
        $this->tokenrechaza = $tokenrechaza;
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
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return AstilleroCotizacion
     */
    public function addPago(\AppBundle\Entity\Pago $pago)
    {
        $pago->setAcotizacion($this);
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
}

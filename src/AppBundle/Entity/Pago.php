<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pago
 *
 * @ORM\Table(name="pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PagoRepository")
 */
class Pago
{
    /**
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Por favor elija un metodo de pago.")
     * @Assert\NotBlank(message="Por favor elija un metodo de pago.")
     *
     * @ORM\Column(name="metodopago", type="string", length=100)
     */
    private $metodopago;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var float
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="dolar", type="float", nullable=true)
     */
    private $dolar;

    /**
     * @var string
     *
     * @ORM\Column(name="titular", type="string", length=255, nullable=true)
     */
    private $titular;

    /**
     * @var string
     *
     * @ORM\Column(name="banco", type="string", length=255, nullable=true)
     */
    private $banco;

    /**
     * @var string
     *
     * @ORM\Column(name="numcuenta", type="string", length=255, nullable=true)
     */
    private $numcuenta;

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
    private $fecharealpago;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", inversedBy="pagos")
     */
    private $mhcotizacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="pagos")
     */
    private $acotizacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Solicitud", inversedBy="pagos")
     */
    private $tiendasolicitud;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CuentaBancaria", inversedBy="pagos")
     * @ORM\JoinColumn(name="idcuentabancaria", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cuentabancaria;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="fecha_registro", type="datetime_immutable", nullable=true)
     */
    private $fechaRegistro;

    /**
     * @var Facturacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion", inversedBy="pagos", cascade={"persist"})
     */
    private $factura;

    public function __construct()
    {
        $this->fechaRegistro = new \DateTimeImmutable();
    }

    public function __toString()
    {
        return $this->metodopago;
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
     * Set metodopago
     *
     * @param string $metodopago
     *
     * @return Pago
     */
    public function setMetodopago($metodopago)
    {
        $this->metodopago = $metodopago;

        return $this;
    }

    /**
     * Get metodopago
     *
     * @return string
     */
    public function getMetodopago()
    {
        return $this->metodopago;
    }


    /**
     * @param \DateTime $fecharealpago
     */
    public function setFecharealpago($fecharealpago)
    {
        $this->fecharealpago = $fecharealpago;
    }

    /**
     * @return \DateTime
     */
    public function getFecharealpago()
    {
        return $this->fecharealpago;
    }

    /**
     * Set cuentabancaria
     *
     * @param CuentaBancaria $cuentabancaria
     *
     * @return Pago
     */
    public function setCuentabancaria(CuentaBancaria $cuentabancaria = null)
    {
        $this->cuentabancaria = $cuentabancaria;

        return $this;
    }

    /**
     * Get cuentabancaria
     *
     * @return CuentaBancaria
     */
    public function getCuentabancaria()
    {
        return $this->cuentabancaria;
    }

    /**
     * @return string
     */
    public function getTitular()
    {
        return $this->titular;
    }

    /**
     * @param string $titular
     */
    public function setTitular($titular)
    {
        $this->titular = $titular;
    }

    /**
     * @return string
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * @param string $banco
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;
    }

    /**
     * @return string
     */
    public function getNumcuenta()
    {
        return $this->numcuenta;
    }

    /**
     * @param string $numcuenta
     */
    public function setNumcuenta($numcuenta)
    {
        $this->numcuenta = $numcuenta;
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
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param float $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * Set mhcotizacion
     *
     * @param MarinaHumedaCotizacion $mhcotizacion
     *
     * @return Pago
     */
    public function setMhcotizacion(MarinaHumedaCotizacion $mhcotizacion = null)
    {
        $this->mhcotizacion = $mhcotizacion;

        return $this;
    }

    /**
     * Get mhcotizacion
     *
     * @return MarinaHumedaCotizacion
     */
    public function getMhcotizacion()
    {
        return $this->mhcotizacion;
    }

    /**
     * @return float
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * @param float $dolar
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;
    }

    /**
     * Set acotizacion
     *
     * @param AstilleroCotizacion $acotizacion
     *
     * @return Pago
     */
    public function setAcotizacion(AstilleroCotizacion $acotizacion = null)
    {
        $this->acotizacion = $acotizacion;

        return $this;
    }

    /**
     * Get acotizacion
     *
     * @return AstilleroCotizacion
     */
    public function getAcotizacion()
    {
        return $this->acotizacion;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTimeImmutable|null $fechaRegistro
     *
     * @return Pago
     */
    public function setFechaRegistro($fechaRegistro = null)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro.
     *
     * @return \DateTimeImmutable|null
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set factura
     *
     * @param Facturacion $factura
     *
     * @return Pago
     */
    public function setFactura(Facturacion $factura = null)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return Facturacion
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * @param string $divisa
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;
    }

    /**
     * Set tiendasolicitud.
     *
     * @param \AppBundle\Entity\Tienda\Solicitud|null $tiendasolicitud
     *
     * @return Pago
     */
    public function setTiendasolicitud(\AppBundle\Entity\Tienda\Solicitud $tiendasolicitud = null)
    {
        $this->tiendasolicitud = $tiendasolicitud;

        return $this;
    }

    /**
     * Get tiendasolicitud.
     *
     * @return \AppBundle\Entity\Tienda\Solicitud|null
     */
    public function getTiendasolicitud()
    {
        return $this->tiendasolicitud;
    }
}

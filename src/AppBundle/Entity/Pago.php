<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="metodopago", type="string", length=100)
     */
    private $metodopago;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var float
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CuentaBancaria", inversedBy="pagos")
     * @ORM\JoinColumn(name="idcuentabancaria", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cuentabancaria;

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
     * @param \AppBundle\Entity\CuentaBancaria $cuentabancaria
     *
     * @return Pago
     */
    public function setCuentabancaria(\AppBundle\Entity\CuentaBancaria $cuentabancaria = null)
    {
        $this->cuentabancaria = $cuentabancaria;

        return $this;
    }

    /**
     * Get cuentabancaria
     *
     * @return \AppBundle\Entity\CuentaBancaria
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
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacion
     *
     * @return Pago
     */
    public function setMhcotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacion = null)
    {
        $this->mhcotizacion = $mhcotizacion;

        return $this;
    }

    /**
     * Get mhcotizacion
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion
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
}

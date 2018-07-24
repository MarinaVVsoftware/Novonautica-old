<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaBancaria
 *
 * @ORM\Table(name="cuenta_bancaria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CuentaBancariaRepository")
 */
class CuentaBancaria
{
    const EMPRESA_MARINA = 1;
    const EMPRESA_ASTILLERO = 2;
    const MONEDA_PESOS = 1;
    const MONEDA_DOLARES = 2;

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
     * @ORM\Column(name="banco", type="string", length=80)
     */
    private $banco;

    /**
     * @var string
     *
     * @ORM\Column(name="sucursal", type="string", length=100)
     */
    private $sucursal;

     /**
     * @var string
     *
     * @ORM\Column(name="clabe", type="string", length=100)
     */
    private $clabe;

    /**
     * @var string
     *
     * @ORM\Column(name="num_cuenta", type="string", length=100)
     */
    private $numCuenta;

    /**
     * @var int
     *
     * @ORM\Column(name="empresa", type="smallint")
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="rfc", type="string", length=100)
     */
    private $rfc;

    /**
     * @var int
     *
     * @ORM\Column(name="moneda", type="smallint")
     */
    private $moneda;

    /**
     *
     * @ORM\OneToMany(targetEntity="Pago", mappedBy="cuentabancaria",cascade={"persist"})
     */
    private $pagos;

    private static $empresaLista = [
        CuentaBancaria::EMPRESA_MARINA => 'Marina',
        CuentaBancaria::EMPRESA_ASTILLERO => 'Astillero'
    ];

    private static $monedaLista = [
      CuentaBancaria::MONEDA_PESOS => 'Pesos',
      CuentaBancaria::MONEDA_DOLARES => 'Dolares'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pagos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
     return $this->banco." ".$this->clabe;
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
     * Set banco
     *
     * @param string $banco
     *
     * @return CuentaBancaria
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return string
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set clabe
     *
     * @param string $clabe
     *
     * @return CuentaBancaria
     */
    public function setClabe($clabe)
    {
        $this->clabe = $clabe;

        return $this;
    }

    /**
     * Get clabe
     *
     * @return string
     */
    public function getClabe()
    {
        return $this->clabe;
    }

    /**
     * Add pago
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return CuentaBancaria
     */
    public function addPago(\AppBundle\Entity\Pago $pago)
    {
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
     * Set sucursal.
     *
     * @param string $sucursal
     *
     * @return CuentaBancaria
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;

        return $this;
    }

    /**
     * Get sucursal.
     *
     * @return string
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * Set numCuenta.
     *
     * @param string $numCuenta
     *
     * @return CuentaBancaria
     */
    public function setNumCuenta($numCuenta)
    {
        $this->numCuenta = $numCuenta;

        return $this;
    }

    /**
     * Get numCuenta.
     *
     * @return string
     */
    public function getNumCuenta()
    {
        return $this->numCuenta;
    }

    /**
     * Set razonSocial.
     *
     * @param string $razonSocial
     *
     * @return CuentaBancaria
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial.
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set rfc.
     *
     * @param string $rfc
     *
     * @return CuentaBancaria
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;

        return $this;
    }

    /**
     * Get rfc.
     *
     * @return string
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * @return int
     */
    public function getEmpresa()
    {
        if (null === $this->empresa) { return null; }

        return $this->empresa;
    }

    /**
     * @return int
     */
    public function getEmpresaNombre()
    {
        if (null === $this->empresa) { return null; }

        return self::$empresaLista[$this->empresa];
    }

    /**
     * @param int $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    public static function getEmpresaLista()
    {
        return self::$empresaLista;
    }

    /**
     * @return int
     */
    public function getMoneda()
    {
        if (null === $this->moneda) { return null; }

        return $this->moneda;
    }

    /**
     * @return int
     */
    public function getMonedaNombre()
    {
        if (null === $this->moneda) { return null; }

        return self::$monedaLista[$this->moneda];
    }

    /**
     * @param int $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    public static function getMonedaLista()
    {
        return self::$monedaLista;
    }
}

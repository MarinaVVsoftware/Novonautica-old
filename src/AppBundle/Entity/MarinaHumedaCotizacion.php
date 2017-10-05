<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     *
     * @ORM\Column(name="fecha_llegada", type="datetime", nullable=true)
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_salida", type="datetime", nullable=true)
     */
    private $fechaSalida;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_estadia", type="integer", nullable=true)
     */
    private $diasEstadia;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_adicionales", type="integer", nullable=true)
     */
    private $diasAdicionales;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_dia", type="float")
     */
    private $precioDia;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina", type="float", nullable=true)
     */
    private $gasolina;

    /**
     * @var float
     *
     * @ORM\Column(name="agua", type="float")
     */
    private $agua;

    /**
     * @var float
     *
     * @ORM\Column(name="electricidad", type="float")
     */
    private $electricidad;

    /**
     * @var float
     *
     * @ORM\Column(name="dezasolve", type="float", nullable=true)
     */
    private $dezasolve;

    /**
     * @var float
     *
     * @ORM\Column(name="limpieza", type="float", nullable=true)
     */
    private $limpieza;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id")
     */
    private $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="mhcotizaciones")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id")
     */
    private $barco;

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
     * Set diasEstadia
     *
     * @param integer $diasEstadia
     *
     * @return MarinaHumedaCotizacion
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
     * Set diasAdicionales
     *
     * @param integer $diasAdicionales
     *
     * @return MarinaHumedaCotizacion
     */
    public function setDiasAdicionales($diasAdicionales)
    {
        $this->diasAdicionales = $diasAdicionales;

        return $this;
    }

    /**
     * Get diasAdicionales
     *
     * @return int
     */
    public function getDiasAdicionales()
    {
        return $this->diasAdicionales;
    }

    /**
     * Set precioDia
     *
     * @param float $precioDia
     *
     * @return MarinaHumedaCotizacion
     */
    public function setPrecioDia($precioDia)
    {
        $this->precioDia = $precioDia;

        return $this;
    }

    /**
     * Get precioDia
     *
     * @return float
     */
    public function getPrecioDia()
    {
        return $this->precioDia;
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
     * Set gasolina
     *
     * @param float $gasolina
     *
     * @return MarinaHumedaCotizacion
     */
    public function setGasolina($gasolina)
    {
        $this->gasolina = $gasolina;

        return $this;
    }

    /**
     * Get gasolina
     *
     * @return float
     */
    public function getGasolina()
    {
        return $this->gasolina;
    }

    /**
     * Set agua
     *
     * @param float $agua
     *
     * @return MarinaHumedaCotizacion
     */
    public function setAgua($agua)
    {
        $this->agua = $agua;

        return $this;
    }

    /**
     * Get agua
     *
     * @return float
     */
    public function getAgua()
    {
        return $this->agua;
    }

    /**
     * Set electricidad
     *
     * @param float $electricidad
     *
     * @return MarinaHumedaCotizacion
     */
    public function setElectricidad($electricidad)
    {
        $this->electricidad = $electricidad;

        return $this;
    }

    /**
     * Get electricidad
     *
     * @return float
     */
    public function getElectricidad()
    {
        return $this->electricidad;
    }

    /**
     * Set dezasolve
     *
     * @param float $dezasolve
     *
     * @return MarinaHumedaCotizacion
     */
    public function setDezasolve($dezasolve)
    {
        $this->dezasolve = $dezasolve;

        return $this;
    }

    /**
     * Get dezasolve
     *
     * @return float
     */
    public function getDezasolve()
    {
        return $this->dezasolve;
    }

    /**
     * Set limpieza
     *
     * @param float $limpieza
     *
     * @return MarinaHumedaCotizacion
     */
    public function setLimpieza($limpieza)
    {
        $this->limpieza = $limpieza;

        return $this;
    }

    /**
     * Get limpieza
     *
     * @return float
     */
    public function getLimpieza()
    {
        return $this->limpieza;
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

}


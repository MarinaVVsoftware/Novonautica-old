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
     * @ORM\Column(name="fecha_llegada", type="datetime")
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_salida", type="datetime")
     */
    private $fechaSalida;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_estadia", type="integer")
     */
    private $diasEstadia;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_estadia_iva", type="float")
     */
    private $diasEstadiaIva;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_estadia_descuento", type="float")
     */
    private $diasEstadiaDescuento;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_estadia_subtotal", type="float")
     */
    private $diasEstadiaSubtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_precio_unidad", type="float")
     */
    private $diasPrecioUnidad;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_adicionales", type="integer", nullable=true)
     */
    private $diasAdicionales;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_adicionales_iva", type="float", nullable=true)
     */
    private $diasAdicionalesIva;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_adicionales_descuento", type="flaot", nullable=true)
     */
    private $diasAdicionalesDescuento;

    /**
     * @var float
     *
     * @ORM\Column(name="dias_adicionales_Subtotal", type="float", nullable=true)
     */
    private $diasAdicionalesSubtotal;


    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina_litros", type="float", nullable=true)
     */
    private $gasolinaLitros;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina_precio", type="float", nullable=true)
     */
    private $gasolinaPrecio;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina_iva", type="float", nullable=true)
     */
    private $gasolinaIva;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina_descuento", type="float", nullable=true)
     */
    private $gasolinaDescuento;

    /**
     * @var float
     *
     * @ORM\Column(name="gasolina_subtotal", type="float", nullable=true)
     */
    private $gasolinaSubtotal;

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
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=true)
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

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
     * Set gasolinalitros
     *
     * @param float $gasolinalitros
     *
     * @return MarinaHumedaCotizacion
     */
    public function setGasolinalitros($gasolinalitros)
    {
        $this->gasolinalitros = $gasolinalitros;

        return $this;
    }

    /**
     * Get gasolinalitros
     *
     * @return float
     */
    public function getGasolinalitros()
    {
        return $this->gasolinalitros;
    }

    /**
     * Set gasolinaprecio
     *
     * @param float $gasolinaprecio
     *
     * @return MarinaHumedaCotizacion
     */
    public function setGasolinaprecio($gasolinaprecio)
    {
        $this->gasolinaprecio = $gasolinaprecio;

        return $this;
    }

    /**
     * Get gasolinaprecio
     *
     * @return float
     */
    public function getGasolinaprecio()
    {
        return $this->gasolinaprecio;
    }

    /**
     * Set gasolinatotal
     *
     * @param float $gasolinatotal
     *
     * @return MarinaHumedaCotizacion
     */
    public function setGasolinatotal($gasolinatotal)
    {
        $this->gasolinatotal = $gasolinatotal;

        return $this;
    }

    /**
     * Get gasolinatotal
     *
     * @return float
     */
    public function getGasolinatotal()
    {
        return $this->gasolinatotal;
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

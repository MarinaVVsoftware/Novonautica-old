<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizaServicios", mappedBy="marinahumedacotizacion",cascade={"persist"})
     */
    private $mhcservicios;

    public function __construct() {
        $this->mhcservicios = new ArrayCollection();
    }
//    public function __toString()
//    {
//        return $this->;
//    }

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

//    /**
//     * Set diasEstadia
//     *
//     * @param integer $diasEstadia
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasEstadia($diasEstadia)
//    {
//        $this->diasEstadia = $diasEstadia;
//
//        return $this;
//    }
//
//    /**
//     * Get diasEstadia
//     *
//     * @return int
//     */
//    public function getDiasEstadia()
//    {
//        return $this->diasEstadia;
//    }
//
//    /**
//     * Set diasEstadiaIva
//     *
//     * @param float $diasEstadiaIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasEstadiaIva($diasEstadiaIva)
//    {
//        $this->diasEstadiaIva = $diasEstadiaIva;
//
//        return $this;
//    }
//
//    /**
//     * Get diasEstadiaIva
//     *
//     * @return float
//     */
//    public function getDiasEstadiaIva()
//    {
//        return $this->diasEstadiaIva;
//    }
//
//    /**
//     * Set diasEstadiaDescuento
//     *
//     * @param float $diasEstadiaDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasEstadiaDescuento($diasEstadiaDescuento)
//    {
//        $this->diasEstadiaDescuento = $diasEstadiaDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get diasEstadiaDescuento
//     *
//     * @return float
//     */
//    public function getDiasEstadiaDescuento()
//    {
//        return $this->diasEstadiaDescuento;
//    }
//
//    /**
//     * Set diasEstadiaTotal
//     *
//     * @param float $diasEstadiaTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasEstadiaTotal($diasEstadiaTotal)
//    {
//        $this->diasEstadiaTotal = $diasEstadiaTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get diasEstadiaTotal
//     *
//     * @return float
//     */
//    public function getDiasEstadiaTotal()
//    {
//        return $this->diasEstadiaTotal;
//    }
//
//    /**
//     * Set diasPrecio
//     *
//     * @param float $diasPrecio
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasPrecio($diasPrecio)
//    {
//        $this->diasPrecio = $diasPrecio;
//
//        return $this;
//    }
//
//    /**
//     * Get diasPrecio
//     *
//     * @return float
//     */
//    public function getDiasPrecio()
//    {
//        return $this->diasPrecio;
//    }
//
//    /**
//     * Set diasAdicionales
//     *
//     * @param integer $diasAdicionales
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasAdicionales($diasAdicionales)
//    {
//        $this->diasAdicionales = $diasAdicionales;
//
//        return $this;
//    }
//
//    /**
//     * Get diasAdicionales
//     *
//     * @return int
//     */
//    public function getDiasAdicionales()
//    {
//        return $this->diasAdicionales;
//    }
//
//    /**
//     * Set diasAdicionalesIva
//     *
//     * @param float $diasAdicionalesIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasAdicionalesIva($diasAdicionalesIva)
//    {
//        $this->diasAdicionalesIva = $diasAdicionalesIva;
//
//        return $this;
//    }
//
//    /**
//     * Get diasAdicionalesIva
//     *
//     * @return float
//     */
//    public function getDiasAdicionalesIva()
//    {
//        return $this->diasAdicionalesIva;
//    }
//
//    /**
//     * Set diasAdicionalesDescuento
//     *
//     * @param float $diasAdicionalesDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasAdicionalesDescuento($diasAdicionalesDescuento)
//    {
//        $this->diasAdicionalesDescuento = $diasAdicionalesDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get diasAdicionalesDescuento
//     *
//     * @return float
//     */
//    public function getDiasAdicionalesDescuento()
//    {
//        return $this->diasAdicionalesDescuento;
//    }
//
//    /**
//     * Set diasAdicionalesTotal
//     *
//     * @param float $diasAdicionalesTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDiasAdicionalesTotal($diasAdicionalesTotal)
//    {
//        $this->diasAdicionalesTotal = $diasAdicionalesTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get diasAdicionalesTotal
//     *
//     * @return float
//     */
//    public function getDiasAdicionalesTotal()
//    {
//        return $this->diasAdicionalesTotal;
//    }
//
//    /**
//     * Set gasolinaLitros
//     *
//     * @param float $gasolinaLitros
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setGasolinaLitros($gasolinaLitros)
//    {
//        $this->gasolinaLitros = $gasolinaLitros;
//
//        return $this;
//    }
//
//    /**
//     * Get gasolinaLitros
//     *
//     * @return float
//     */
//    public function getGasolinaLitros()
//    {
//        return $this->gasolinaLitros;
//    }
//
//    /**
//     * Set gasolinaPrecio
//     *
//     * @param float $gasolinaPrecio
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setGasolinaPrecio($gasolinaPrecio)
//    {
//        $this->gasolinaPrecio = $gasolinaPrecio;
//
//        return $this;
//    }
//
//    /**
//     * Get gasolinaPrecio
//     *
//     * @return float
//     */
//    public function getGasolinaPrecio()
//    {
//        return $this->gasolinaPrecio;
//    }
//
//    /**
//     * Set gasolinaIva
//     *
//     * @param float $gasolinaIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setGasolinaIva($gasolinaIva)
//    {
//        $this->gasolinaIva = $gasolinaIva;
//
//        return $this;
//    }
//
//    /**
//     * Get gasolinaIva
//     *
//     * @return float
//     */
//    public function getGasolinaIva()
//    {
//        return $this->gasolinaIva;
//    }
//
//    /**
//     * Set gasolinaDescuento
//     *
//     * @param float $gasolinaDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setGasolinaDescuento($gasolinaDescuento)
//    {
//        $this->gasolinaDescuento = $gasolinaDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get gasolinaDescuento
//     *
//     * @return float
//     */
//    public function getGasolinaDescuento()
//    {
//        return $this->gasolinaDescuento;
//    }
//
//    /**
//     * Set gasolinaTotal
//     *
//     * @param float $gasolinaTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setGasolinaTotal($gasolinaTotal)
//    {
//        $this->gasolinaTotal = $gasolinaTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get gasolinaTotal
//     *
//     * @return float
//     */
//    public function getGasolinaTotal()
//    {
//        return $this->gasolinaTotal;
//    }
//
//    /**
//     * Set electricidad
//     *
//     * @param float $electricidad
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setElectricidad($electricidad)
//    {
//        $this->electricidad = $electricidad;
//
//        return $this;
//    }
//
//    /**
//     * Get electricidad
//     *
//     * @return float
//     */
//    public function getElectricidad()
//    {
//        return $this->electricidad;
//    }
//
//    /**
//     * Set aguaPrecio
//     *
//     * @param float $aguaPrecio
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setAguaPrecio($aguaPrecio)
//    {
//        $this->aguaPrecio = $aguaPrecio;
//
//        return $this;
//    }
//
//    /**
//     * Get aguaPrecio
//     *
//     * @return float
//     */
//    public function getAguaPrecio()
//    {
//        return $this->aguaPrecio;
//    }
//
//    /**
//     * Set aguaIva
//     *
//     * @param float $aguaIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setAguaIva($aguaIva)
//    {
//        $this->aguaIva = $aguaIva;
//
//        return $this;
//    }
//
//    /**
//     * Get aguaIva
//     *
//     * @return float
//     */
//    public function getAguaIva()
//    {
//        return $this->aguaIva;
//    }
//
//    /**
//     * Set aguaDescuento
//     *
//     * @param float $aguaDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setAguaDescuento($aguaDescuento)
//    {
//        $this->aguaDescuento = $aguaDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get aguaDescuento
//     *
//     * @return float
//     */
//    public function getAguaDescuento()
//    {
//        return $this->aguaDescuento;
//    }
//
//    /**
//     * Set aguaTotal
//     *
//     * @param float $aguaTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setAguaTotal($aguaTotal)
//    {
//        $this->aguaTotal = $aguaTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get aguaTotal
//     *
//     * @return float
//     */
//    public function getAguaTotal()
//    {
//        return $this->aguaTotal;
//    }
//
//    /**
//     * Set dezasolvePrecio
//     *
//     * @param float $dezasolvePrecio
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDezasolvePrecio($dezasolvePrecio)
//    {
//        $this->dezasolvePrecio = $dezasolvePrecio;
//
//        return $this;
//    }
//
//    /**
//     * Get dezasolvePrecio
//     *
//     * @return float
//     */
//    public function getDezasolvePrecio()
//    {
//        return $this->dezasolvePrecio;
//    }
//
//    /**
//     * Set dezasolveIva
//     *
//     * @param float $dezasolveIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDezasolveIva($dezasolveIva)
//    {
//        $this->dezasolveIva = $dezasolveIva;
//
//        return $this;
//    }
//
//    /**
//     * Get dezasolveIva
//     *
//     * @return float
//     */
//    public function getDezasolveIva()
//    {
//        return $this->dezasolveIva;
//    }
//
//    /**
//     * Set dezasolveDescuento
//     *
//     * @param float $dezasolveDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDezasolveDescuento($dezasolveDescuento)
//    {
//        $this->dezasolveDescuento = $dezasolveDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get dezasolveDescuento
//     *
//     * @return float
//     */
//    public function getDezasolveDescuento()
//    {
//        return $this->dezasolveDescuento;
//    }
//
//    /**
//     * Set dezasolveTotal
//     *
//     * @param float $dezasolveTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setDezasolveTotal($dezasolveTotal)
//    {
//        $this->dezasolveTotal = $dezasolveTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get dezasolveTotal
//     *
//     * @return float
//     */
//    public function getDezasolveTotal()
//    {
//        return $this->dezasolveTotal;
//    }
//
//    /**
//     * Set limpiezaPrecio
//     *
//     * @param float $limpiezaPrecio
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setLimpiezaPrecio($limpiezaPrecio)
//    {
//        $this->limpiezaPrecio = $limpiezaPrecio;
//
//        return $this;
//    }
//
//    /**
//     * Get limpiezaPrecio
//     *
//     * @return float
//     */
//    public function getLimpiezaPrecio()
//    {
//        return $this->limpiezaPrecio;
//    }
//
//    /**
//     * Set limpiezaIva
//     *
//     * @param float $limpiezaIva
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setLimpiezaIva($limpiezaIva)
//    {
//        $this->limpiezaIva = $limpiezaIva;
//
//        return $this;
//    }
//
//    /**
//     * Get limpiezaIva
//     *
//     * @return float
//     */
//    public function getLimpiezaIva()
//    {
//        return $this->limpiezaIva;
//    }
//
//    /**
//     * Set limpiezaDescuento
//     *
//     * @param float $limpiezaDescuento
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setLimpiezaDescuento($limpiezaDescuento)
//    {
//        $this->limpiezaDescuento = $limpiezaDescuento;
//
//        return $this;
//    }
//
//    /**
//     * Get limpiezaDescuento
//     *
//     * @return float
//     */
//    public function getLimpiezaDescuento()
//    {
//        return $this->limpiezaDescuento;
//    }
//
//    /**
//     * Set limpiezaTotal
//     *
//     * @param float $limpiezaTotal
//     *
//     * @return MarinaHumedaCotizacion
//     */
//    public function setLimpiezaTotal($limpiezaTotal)
//    {
//        $this->limpiezaTotal = $limpiezaTotal;
//
//        return $this;
//    }
//
//    /**
//     * Get limpiezaTotal
//     *
//     * @return float
//     */
//    public function getLimpiezaTotal()
//    {
//        return $this->limpiezaTotal;
//    }

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
}

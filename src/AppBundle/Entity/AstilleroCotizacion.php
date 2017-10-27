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

    public function __construct() {
        $this->acservicios = new ArrayCollection();
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
}

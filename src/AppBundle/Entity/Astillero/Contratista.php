<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contratista
 *
 * @ORM\Table(name="astillero_contratista")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ContratistaRepository")
 */
class Contratista
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
     * @ORM\Column(name="cotizacionInicial", type="string", length=255)
     */
    private $cotizacionInicial;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var float
     *
     * @ORM\Column(name="porcentajevv", type="float")
     */
    private $porcentajevv;

    /**
     * @var int
     *
     * @ORM\Column(name="utilidadvv", type="bigint")
     */
    private $utilidadvv;

    /**
     * @var int
     *
     * @ORM\Column(name="preciovv", type="bigint")
     */
    private $preciovv;

    /**
     * @var int
     *
     * @ORM\Column(name="ivatot", type="bigint")
     */
    private $ivatot;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var int
     *
     * @ORM\Column(name="pagos", type="bigint")
     */
    private $pagos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="formaPago", type="string", length=255)
     */
    private $formaPago;

    /**
     * @var int
     *
     * @ORM\Column(name="saldo", type="bigint")
     */
    private $saldo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OrdenDeTrabajo", inversedBy="contratistas")
     * @ORM\JoinColumn(name="idodt", referencedColumnName="id",onDelete="CASCADE")
     */
    private $astilleroODT;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Proveedor", inversedBy="AContratistas")
     * @ORM\JoinColumn(name="idproveedor", referencedColumnName="id")
     */
    private $proveedor;

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
     * Set cotizacionInicial.
     *
     * @param string $cotizacionInicial
     *
     * @return Contratista
     */
    public function setCotizacionInicial($cotizacionInicial)
    {
        $this->cotizacionInicial = $cotizacionInicial;

        return $this;
    }

    /**
     * Get cotizacionInicial.
     *
     * @return string
     */
    public function getCotizacionInicial()
    {
        return $this->cotizacionInicial;
    }

    /**
     * Set precio.
     *
     * @param int $precio
     *
     * @return Contratista
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set ivatot.
     *
     * @param int $ivatot
     *
     * @return Contratista
     */
    public function setIvatot($ivatot)
    {
        $this->ivatot = $ivatot;

        return $this;
    }

    /**
     * Get ivatot.
     *
     * @return int
     */
    public function getIvatot()
    {
        return $this->ivatot;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Contratista
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set astilleroODT.
     *
     * @param \AppBundle\Entity\OrdenDeTrabajo|null $astilleroODT
     *
     * @return Contratista
     */
    public function setAstilleroODT(\AppBundle\Entity\OrdenDeTrabajo $astilleroODT = null)
    {
        $this->astilleroODT = $astilleroODT;

        return $this;
    }

    /**
     * Get astilleroODT.
     *
     * @return \AppBundle\Entity\OrdenDeTrabajo|null
     */
    public function getAstilleroODT()
    {
        return $this->astilleroODT;
    }

    /**
     * Set proveedor.
     *
     * @param \AppBundle\Entity\Astillero\Proveedor|null $proveedor
     *
     * @return Contratista
     */
    public function setProveedor(\AppBundle\Entity\Astillero\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return \AppBundle\Entity\Astillero\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @return float
     */
    public function getPorcentajevv()
    {
        return $this->porcentajevv;
    }

    /**
     * @param float $porcentajevv
     * @return Contratista
     */
    public function setPorcentajevv($porcentajevv)
    {
        $this->porcentajevv = $porcentajevv;
        return $this;
    }

    /**
     * @return int
     */
    public function getUtilidadvv()
    {
        return $this->utilidadvv;
    }

    /**
     * @param int $utilidadvv
     */
    public function setUtilidadvv($utilidadvv)
    {
        $this->utilidadvv = $utilidadvv;
    }

    /**
     * @return int
     */
    public function getPreciovv()
    {
        return $this->preciovv;
    }

    /**
     * @param int $preciovv
     */
    public function setPreciovv($preciovv)
    {
        $this->preciovv = $preciovv;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Contratista
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
     * Set formaPago.
     *
     * @param string $formaPago
     *
     * @return Contratista
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago.
     *
     * @return string
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set saldo.
     *
     * @param int $saldo
     *
     * @return Contratista
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo.
     *
     * @return int
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set pagos.
     *
     * @param int $pagos
     *
     * @return Contratista
     */
    public function setPagos($pagos)
    {
        $this->pagos = $pagos;

        return $this;
    }

    /**
     * Get pagos.
     *
     * @return int
     */
    public function getPagos()
    {
        return $this->pagos;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenDeTrabajo
 *
 * @ORM\Table(name="orden_de_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenDeTrabajoRepository")
 */
class OrdenDeTrabajo
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
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="precioTotal", type="bigint")
     */
    private $precioTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="utilidadvvTotal", type="bigint")
     */
    private $utilidadvvTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="preciovvTotal", type="bigint")
     */
    private $preciovvTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="ivaTotal", type="bigint")
     */
    private $ivaTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="granTotal", type="bigint")
     */
    private $granTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="materialesTotal", type="bigint", nullable=true)
     */
    private $materialesTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="pagosTotal", type="bigint", nullable=true)
     */
    private $pagosTotal;

    /**
     * @var int
     *
     * @ORM\Column(name="saldoTotal", type="bigint")
     */
    private $saldoTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="odt")
     * @ORM\JoinColumn(name="idastillerocotizacion", referencedColumnName="id")
     */
    private $astilleroCotizacion;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Astillero\Contratista", mappedBy="astilleroODT", cascade={"persist"})
     */
    private $contratistas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contratistas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return OrdenDeTrabajo
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set astilleroCotizacion
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astilleroCotizacion
     *
     * @return OrdenDeTrabajo
     */
    public function setAstilleroCotizacion(\AppBundle\Entity\AstilleroCotizacion $astilleroCotizacion = null)
    {
        $this->astilleroCotizacion = $astilleroCotizacion;

        return $this;
    }

    /**
     * Get astilleroCotizacion
     *
     * @return \AppBundle\Entity\AstilleroCotizacion
     */
    public function getAstilleroCotizacion()
    {
        return $this->astilleroCotizacion;
    }

    /**
     * Add contratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista $contratista
     *
     * @return OrdenDeTrabajo
     */
    public function addContratista(\AppBundle\Entity\Astillero\Contratista $contratista)
    {
        $contratista->setAstilleroODT($this);
        $this->contratistas[] = $contratista;
        return $this;
    }

    /**
     * Remove contratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista $contratista
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeContratista(\AppBundle\Entity\Astillero\Contratista $contratista)
    {
        return $this->contratistas->removeElement($contratista);
    }

    /**
     * Get contratistas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContratistas()
    {
        return $this->contratistas;
    }

    /**
     * @return int
     */
    public function getPrecioTotal()
    {
        return $this->precioTotal;
    }

    /**
     * @param int $precioTotal
     * @return OrdenDeTrabajo
     */
    public function setPrecioTotal($precioTotal)
    {
        $this->precioTotal = $precioTotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getUtilidadvvTotal()
    {
        return $this->utilidadvvTotal;
    }

    /**
     * @param int $utilidadvvTotal
     * @return OrdenDeTrabajo
     */
    public function setUtilidadvvTotal($utilidadvvTotal)
    {
        $this->utilidadvvTotal = $utilidadvvTotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getPreciovvTotal()
    {
        return $this->preciovvTotal;
    }

    /**
     * @param int $preciovvTotal
     * @return OrdenDeTrabajo
     */
    public function setPreciovvTotal($preciovvTotal)
    {
        $this->preciovvTotal = $preciovvTotal;
        return $this;
    }


    /**
     * Set ivaTotal.
     *
     * @param int $ivaTotal
     *
     * @return OrdenDeTrabajo
     */
    public function setIvaTotal($ivaTotal)
    {
        $this->ivaTotal = $ivaTotal;

        return $this;
    }

    /**
     * Get ivaTotal.
     *
     * @return int
     */
    public function getIvaTotal()
    {
        return $this->ivaTotal;
    }

    /**
     * Set granTotal.
     *
     * @param int $granTotal
     *
     * @return OrdenDeTrabajo
     */
    public function setGranTotal($granTotal)
    {
        $this->granTotal = $granTotal;

        return $this;
    }

    /**
     * Get granTotal.
     *
     * @return int
     */
    public function getGranTotal()
    {
        return $this->granTotal;
    }

    /**
     * Set pagosTotal.
     *
     * @param int $pagosTotal
     *
     * @return OrdenDeTrabajo
     */
    public function setPagosTotal($pagosTotal)
    {
        $this->pagosTotal = $pagosTotal;

        return $this;
    }

    /**
     * Get pagosTotal.
     *
     * @return int
     */
    public function getPagosTotal()
    {
        return $this->pagosTotal;
    }

    /**
     * Set saldoTotal.
     *
     * @param int $saldoTotal
     *
     * @return OrdenDeTrabajo
     */
    public function setSaldoTotal($saldoTotal)
    {
        $this->saldoTotal = $saldoTotal;

        return $this;
    }

    /**
     * Get saldoTotal.
     *
     * @return int
     */
    public function getSaldoTotal()
    {
        return $this->saldoTotal;
    }

    /**
     * Set materialesTotal.
     *
     * @param int|null $materialesTotal
     *
     * @return OrdenDeTrabajo
     */
    public function setMaterialesTotal($materialesTotal = null)
    {
        $this->materialesTotal = $materialesTotal;

        return $this;
    }

    /**
     * Get materialesTotal.
     *
     * @return int|null
     */
    public function getMaterialesTotal()
    {
        return $this->materialesTotal;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return OrdenDeTrabajo
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
}

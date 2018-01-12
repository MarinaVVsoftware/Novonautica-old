<?php

namespace AppBundle\Entity\Contabilidad\Facturacion\Concepto;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto;
use Doctrine\ORM\Mapping as ORM;

/**
 * Impuesto
 *
 * @ORM\Table(name="contabilidad_facturacion_concepto_impuesto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\Concepto\ImpuestoRepository")
 */
class Impuesto
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
     * @var int
     *
     * @ORM\Column(name="base", type="bigint")
     */
    private $base;

    /**
     * @var string
     *
     * @ORM\Column(name="impuesto", type="string", length=20)
     */
    private $impuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_factor", type="string", length=50)
     */
    private $tipoFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="tasaocuota", type="string", length=50)
     */
    private $tasaocuota;

    /**
     * @var int
     *
     * @ORM\Column(name="importe", type="bigint")
     */
    private $importe;

    /**
     * @var Concepto
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto", inversedBy="impuestos")
     */
    private $concepto;

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
     * Set base
     *
     * @param integer $base
     *
     * @return Impuesto
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return int
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set impuesto
     *
     * @param string $impuesto
     *
     * @return Impuesto
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto
     *
     * @return string
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set tipoFactor
     *
     * @param string $tipoFactor
     *
     * @return Impuesto
     */
    public function setTipoFactor($tipoFactor)
    {
        $this->tipoFactor = $tipoFactor;

        return $this;
    }

    /**
     * Get tipoFactor
     *
     * @return string
     */
    public function getTipoFactor()
    {
        return $this->tipoFactor;
    }

    /**
     * Set tasaocuota
     *
     * @param string $tasaocuota
     *
     * @return Impuesto
     */
    public function setTasaocuota($tasaocuota)
    {
        $this->tasaocuota = $tasaocuota;

        return $this;
    }

    /**
     * Get tasaocuota
     *
     * @return string
     */
    public function getTasaocuota()
    {
        return $this->tasaocuota;
    }

    /**
     * Set importe
     *
     * @param integer $importe
     *
     * @return Impuesto
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return int
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set concepto
     *
     * @param Concepto $concepto
     *
     * @return Impuesto
     */
    public function setConcepto(Concepto $concepto = null)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return Concepto
     */
    public function getConcepto()
    {
        return $this->concepto;
    }
}

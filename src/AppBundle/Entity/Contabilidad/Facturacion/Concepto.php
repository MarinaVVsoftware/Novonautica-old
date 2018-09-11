<?php

namespace AppBundle\Entity\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="contabilidad_facturacion_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\ConceptoRepository")
 */
class Concepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*------------------------------------------------------------------------------------------------
     * DATOS DE CONCEPTO
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="unidad", type="string", length=20)
     */
    private $unidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="valor_unitario", type="bigint")
     */
    private $valorUnitario;

    /**
     * @var integer
     *
     * @ORM\Column(name="importe", type="bigint")
     */
    private $importe;

    /*------------------------------------------------------------------------------------------------*/

    /*------------------------------------------------------------------------------------------------
     * DATOS DE IMPUESTOS ( Deberia ser una coleccion de impuestos, pero ahora solo se agregara uno (iva)
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var int
     *
     * @ORM\Column(name="base", type="bigint")
     */
    private $base;

    /**
     * @var string
     *
     * @ORM\Column(name="impuesto", type="string", length=10)
     */
    private $impuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_factor", type="string", length=20)
     */
    private $tipoFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="tasa_o_cuota", type="string", length=10)
     */
    private $tasaOCuota;

    /**
     * @var int
     *
     * @ORM\Column(name="impuesto_importe", type="bigint")
     */
    private $impuestoImporte;

    /*------------------------------------------------------------------------------------------------*/

    /*------------------------------------------------------------------------------------------------
     * ENTIDADES RELACIONADAS
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @var Facturacion\Concepto\ClaveProdServ
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

    /**
     * @var Facturacion\Concepto\ClaveUnidad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

    /**
     * @var Facturacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion", inversedBy="conceptos")
     */
    private $factura;

    public function __construct()
    {
        $this->cantidad = 1;
        $this->unidad = 'NA';
        $this->valorUnitario = 0;
        $this->importe = 0;

        $this->base = 0;
        $this->impuesto = Facturacion::$impuestos['IVA'];
        $this->tipoFactor = Facturacion::$factores['Tasa'];
        $this->tasaOCuota = '0.160000';
        $this->impuestoImporte = 0;
    }

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantidad.
     *
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * Get cantidad.
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set unidad.
     *
     * @param string $unidad
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
    }

    /**
     * Get unidad.
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Get descripcion.
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set valorunitario.
     *
     * @param int $valorUnitario
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;
    }

    /**
     * Get valorunitario.
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set importe.
     *
     * @param int $importe
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    /**
     * Get importe.
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set base.
     *
     * @param int $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * Get base.
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set impuesto.
     *
     * @param string $impuesto
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;
    }

    /**
     * Get impuesto.
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set tipoFactor.
     *
     * @param string $tipoFactor
     */
    public function setTipoFactor($tipoFactor)
    {
        $this->tipoFactor = $tipoFactor;
    }

    /**
     * Get tipoFactor.
     */
    public function getTipoFactor()
    {
        return $this->tipoFactor;
    }

    /**
     * Set tasaOCuota.
     *
     * @param string $tasaOCuota
     */
    public function setTasaOCuota($tasaOCuota)
    {
        $this->tasaOCuota = $tasaOCuota;
    }

    /**
     * Get tasaOCuota.
     */
    public function getTasaOCuota()
    {
        return $this->tasaOCuota;
    }

    /**
     * Set impuestoImporte.
     *
     * @param int $impuestoImporte
     */
    public function setImpuestoImporte($impuestoImporte)
    {
        $this->impuestoImporte = $impuestoImporte;
    }

    /**
     * Get impuestoImporte.
     */
    public function getImpuestoImporte()
    {
        return $this->impuestoImporte;
    }

    /**
     * Set claveProdServ.
     *
     * @param Concepto\ClaveProdServ|null $claveProdServ
     */
    public function setClaveProdServ(Concepto\ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;
    }

    /**
     * Get claveProdServ.
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set claveUnidad.
     *
     * @param Concepto\ClaveUnidad|null $claveUnidad
     */
    public function setClaveUnidad(Concepto\ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;
    }

    /**
     * Get claveUnidad.
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set factura.
     *
     * @param Facturacion|null $factura
     */
    public function setFactura(Facturacion $factura = null)
    {
        $this->factura = $factura;
    }

    /**
     * Get factura.
     */
    public function getFactura()
    {
        return $this->factura;
    }
}

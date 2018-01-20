<?php

namespace AppBundle\Entity\Contabilidad\Facturacion;

use AppBundle\Entity\Contabilidad\Facturacion;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Por favor indique una cantidad")
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Por favor indique una unidad")
     *
     * @ORM\Column(name="unidad", type="string", length=100)
     */
    private $unidad;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="No puede dejar vacio este valor")
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="No puede dejar vacio este valor")
     *
     * @ORM\Column(name="valorunitario", type="bigint")
     */
    private $valorunitario;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Por favor indique un descuento")
     *
     * @ORM\Column(name="descuento", type="bigint")
     */
    private $descuento;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="No puede dejar vacio este valor")
     *
     * @ORM\Column(name="iva", type="bigint")
     */
    private $iva;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="No puede dejar vacio este valor")
     *
     *@ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="No puede dejar vacio este valor")
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="clave_prod_serv", type="string", length=20)
     */
    private $claveProdServ;

    /**
     * @var string
     *
     * @ORM\Column(name="clave_unidad", type="string", length=255)
     */
    private $claveUnidad;

    /**
     * @var Facturacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion", inversedBy="conceptos")
     */
    private $factura;

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
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return Concepto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     *
     * @return Concepto
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Concepto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set valorunitario
     *
     * @param integer $valorunitario
     *
     * @return Concepto
     */
    public function setValorunitario($valorunitario)
    {
        $this->valorunitario = $valorunitario;

        return $this;
    }

    /**
     * Get valorunitario
     *
     * @return int
     */
    public function getValorunitario()
    {
        return $this->valorunitario;
    }

    /**
     * Set claveProdServ
     *
     * @param string $claveProdServ
     *
     * @return Concepto
     */
    public function setClaveProdServ($claveProdServ)
    {
        $this->claveProdServ = $claveProdServ;

        return $this;
    }

    /**
     * Get claveProdServ
     *
     * @return string
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set claveUnidad
     *
     * @param string $claveUnidad
     *
     * @return Concepto
     */
    public function setClaveUnidad($claveUnidad)
    {
        $this->claveUnidad = $claveUnidad;

        return $this;
    }

    /**
     * Get claveUnidad
     *
     * @return string
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set factura
     *
     * @param Facturacion $factura
     *
     * @return Concepto
     */
    public function setFactura(Facturacion $factura = null)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return Facturacion
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set descuento
     *
     * @param integer $descuento
     *
     * @return Concepto
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return integer
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set iva
     *
     * @param integer $iva
     *
     * @return Concepto
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return integer
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal
     *
     * @param integer $subtotal
     *
     * @return Concepto
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return integer
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Concepto
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }
}

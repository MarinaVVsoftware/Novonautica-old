<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use AppBundle\Validator\Constraints as NovoAssert;

/**
 * MarinaHumedaCotizaServicios
 *
 * @ORM\Table(name="marina_humeda_cotiza_servicios")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaCotizaServiciosRepository")
 * @ORM\EntityListeners({"AppBundle\Entity\Marina\CotizaServiciosListener"})
 */
class MarinaHumedaCotizaServicios
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
     * Tipo de servicio 1=Estadia, 2=Electricidad
     *
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true)
     */
    private $tipo;

    /**
     * @var float
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="precio", type="integer", nullable=true)
     */
    private $precio;

    private $precioaux;

    private $precioOtro;

    /**
     * Usado para reconocer si un precio viene del input otro precio
     * @var boolean
     *
     * @ORM\Column(name="is_precio_otro", type="boolean", nullable=true)
     */
    private $isPrecioOtro;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="iva", type="bigint", nullable=true)
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="descuento", type="bigint", nullable=true)
     */
    private $descuento;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", inversedBy="mhcservicios")
     * @ORM\JoinColumn(name="idmhcotizacion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $marinahumedacotizacion;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacionAdicional", inversedBy="mhcservicios")
     * @ORM\JoinColumn(name="idmhcotizacionadicional", referencedColumnName="id",onDelete="CASCADE")
     */
    private $marinahumedacotizacionadicional;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarinaHumedaServicio")
     * @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     */
    private $marinahumedaservicio;

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
     * set tipo
     *
     * @param int $tipo
     *
     *  @return MarinaHumedaCotizaServicios
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }


    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precio
     *
     * @param int $precio
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set precioaux
     *
     * @param int $precioaux
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setPrecioAux($precioaux)
    {
        $this->precioaux = $precioaux;

        return $this;
    }

    /**
     * Get precioaux
     *
     * @return int
     */
    public function getPrecioAux()
    {
        return $this->precioaux;
    }

    /**
     * Set precioOtro
     *
     * @param int $precioOtro
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setPrecioOtro($precioOtro)
    {
        $this->precioOtro = $precioOtro;

        return $this;
    }

    /**
     * Get precioOtro
     *
     * @return int
     */
    public function getPrecioOtro()
    {
        return $this->precioOtro;
    }

    /**
     * Set subtotal
     *
     * @param int $subtotal
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set iva
     *
     * @param int $iva
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return int
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set descuento
     *
     * @param int $descuento
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return int
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set total
     *
     * @param int $total
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setMarinaHumedaCotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion = null)
    {
        $this->marinahumedacotizacion = $marinahumedacotizacion;
        return $this;
    }

    /**
     * Get marinahumedacotizacion
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion
     */
    public function getMarinaHumedaCotizacion()
    {
        return $this->marinahumedacotizacion;
    }

    /**
     * Set marinahumedacotizacionadicional
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacionAdicional $marinahumedacotizacionadicional
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setMarinahumedacotizacionadicional(\AppBundle\Entity\MarinaHumedaCotizacionAdicional $marinahumedacotizacionadicional = null)
    {
        $this->marinahumedacotizacionadicional = $marinahumedacotizacionadicional;

        return $this;
    }

    /**
     * Get marinahumedacotizacionadicional
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacionAdicional
     */
    public function getMarinahumedacotizacionadicional()
    {
        return $this->marinahumedacotizacionadicional;
    }

    /**
     * Set marinahumedaservicio
     *
     * @param \AppBundle\Entity\MarinaHumedaServicio $marinahumedaservicio
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setMarinahumedaservicio(\AppBundle\Entity\MarinaHumedaServicio $marinahumedaservicio = null)
    {
        $this->marinahumedaservicio = $marinahumedaservicio;

        return $this;
    }

    /**
     * Get marinahumedaservicio
     *
     * @return \AppBundle\Entity\MarinaHumedaServicio
     */
    public function getMarinahumedaservicio()
    {
        return $this->marinahumedaservicio;
    }

    public function getProducto()
    {
        return $this->marinahumedaservicio;
    }

    /**
     * Set isPrecioOtro.
     *
     * @param bool|null $isPrecioOtro
     *
     * @return MarinaHumedaCotizaServicios
     */
    public function setIsPrecioOtro($isPrecioOtro = null)
    {
        $this->isPrecioOtro = $isPrecioOtro;

        return $this;
    }

    /**
     * Get isPrecioOtro.
     *
     * @return bool|null
     */
    public function getIsPrecioOtro()
    {
        return $this->isPrecioOtro;
    }
}

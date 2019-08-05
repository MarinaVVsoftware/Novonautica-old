<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * AstilleroCotizaServicio
 *
 * @ORM\Table(name="astillero_cotiza_servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AstilleroCotizaServicioRepository")
 */
class AstilleroCotizaServicio
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
     * @var float
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * 0 = cantidad fija, 1 = promedio por pie
     *
     * @var int
     *
     * @ORM\Column(name="tipo_cantidad", type="smallint", nullable=true)
     */
    private $tipoCantidad;

    /**
     * En caso de tipoCantidad = 1 se usa promedio para indicar valor por pie de embarcación
     *
     * @var float
     *
     * @ORM\Column(name="promedio", type="float", nullable=true)
     */
    private $promedio;

    /**
     * En caso de ser producto se guarda el id del servicio con el que esta relacionado
     *
     * @var int
     *
     * @ORM\Column(name="grupo", type="integer", nullable=true)
     */
    private $grupo;

    /**
     * @var decimal
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="precio", type="decimal", nullable=true, precision=19, scale=4)
     */
    private $precio;

    /**
     * @var decimal
     *
     * @ORM\Column(name="subtotal", type="decimal", nullable=true, precision=19, scale=4)
     */
    private $subtotal;

    /**
     * @var decimal
     *
     * @ORM\Column(name="iva", type="decimal", nullable=true, precision=19, scale=4)
     */
    private $iva;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total", type="decimal", nullable=true, precision=19, scale=4)
     */
    private $total;

    /**
     * divisa solo de variable precio, los demas estan en mxn
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=3, nullable=true)
     */
    private $divisa;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="otroservicio", type="string", length=255, nullable=true)
     */
    private $otroservicio;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="acservicios")
     * @ORM\JoinColumn(name="idastillerocotizacion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $astillerocotizacion;

    /**
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AstilleroServicioBasico")
     * @ORM\JoinColumn(name="idserviciobasico", referencedColumnName="id")
     */
    private $astilleroserviciobasico;

    /**
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Producto", inversedBy="ACotizacionesServicios")
     * @ORM\JoinColumn(name="idproducto", referencedColumnName="id")
     */
    private $producto;

    /**
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Servicio")
     * @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     */
    private $servicio;

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
     * Set otroservicio
     *
     * @param string $otroservicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setOtroservicio($otroservicio)
    {
        $this->otroservicio = $otroservicio;

        return $this;
    }

    /**
     * Get otroservicio
     *
     * @return string
     */
    public function getOtroservicio()
    {
        return $this->otroservicio;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return AstilleroCotizaServicio
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
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return AstilleroCotizaServicio
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
     * Set astillerocotizacion
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astillerocotizacion
     *
     * @return AstilleroCotizaServicio
     */
    public function setAstillerocotizacion(\AppBundle\Entity\AstilleroCotizacion $astillerocotizacion = null)
    {
        $this->astillerocotizacion = $astillerocotizacion;

        return $this;
    }

    /**
     * Get astillerocotizacion
     *
     * @return \AppBundle\Entity\AstilleroCotizacion
     */
    public function getAstillerocotizacion()
    {
        return $this->astillerocotizacion;
    }

    /**
     * Set astilleroserviciobasico
     *
     * @param \AppBundle\Entity\AstilleroServicioBasico $astilleroservicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setAstilleroserviciobasico(\AppBundle\Entity\AstilleroServicioBasico $astilleroserviciobasico = null)
    {
        $this->astilleroserviciobasico = $astilleroserviciobasico;

        return $this;
    }

    /**
     * Get astilleroserviciobasico
     *
     * @return \AppBundle\Entity\AstilleroServicioBasico
     */
    public function getAstilleroserviciobasico()
    {
        return $this->astilleroserviciobasico;
    }


    /**
     * Set producto
     *
     * @param \AppBundle\Entity\Astillero\Producto $producto
     *
     * @return AstilleroCotizaServicio
     */
    public function setProducto(\AppBundle\Entity\Astillero\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \AppBundle\Entity\Astillero\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set servicio
     *
     * @param \AppBundle\Entity\Astillero\Servicio $servicio
     *
     * @return AstilleroCotizaServicio
     */
    public function setServicio(\AppBundle\Entity\Astillero\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \AppBundle\Entity\Astillero\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @return decimal
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param decimal $precio
     * @return AstilleroCotizaServicio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param int $subtotal
     * @return AstilleroCotizaServicio
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param int $iva
     * @return AstilleroCotizaServicio
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return AstilleroCotizaServicio
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }


    /**
     * Set divisa.
     *
     * @param string|null $divisa
     *
     * @return AstilleroCotizaServicio
     */
    public function setDivisa($divisa = null)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string|null
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * Set tipoCantidad.
     *
     * @param int|null $tipoCantidad
     *
     * @return AstilleroCotizaServicio
     */
    public function setTipoCantidad($tipoCantidad = null)
    {
        $this->tipoCantidad = $tipoCantidad;

        return $this;
    }

    /**
     * Get tipoCantidad.
     *
     * @return int|null
     */
    public function getTipoCantidad()
    {
        return $this->tipoCantidad;
    }

    /**
     * Set promedio.
     *
     * @param float|null $promedio
     *
     * @return AstilleroCotizaServicio
     */
    public function setPromedio($promedio = null)
    {
        $this->promedio = $promedio;

        return $this;
    }

    /**
     * Get promedio.
     *
     * @return float|null
     */
    public function getPromedio()
    {
        return $this->promedio;
    }

    /**
     * Set grupo.
     *
     * @param int|null $grupo
     *
     * @return AstilleroCotizaServicio
     */
    public function setGrupo($grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo.
     *
     * @return int|null
     */
    public function getGrupo()
    {
        return $this->grupo;
    }
}

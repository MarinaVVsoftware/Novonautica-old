<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Solicitud
 *
 * @ORM\Table(name="tienda_solicitud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\SolicitudRepository")
 */
class Solicitud
{
    /**
     * @var int
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @Groups({"facturacion"})*
     *
     * @ORM\Column(name="folio", type="integer")
     */
    private $folio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="embarcacion")
     */
    private $nombrebarco;

    /**
     * @Groups({"facturacion"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Peticion", mappedBy="solicitud", cascade={"persist"})
     */
    private $producto;

    /**
     * @var string
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="solicitud_especial", type="string", length=255, nullable=true)
     */
    private $solicitudEspecial;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="preciosolespecial", type="bigint", length=20, nullable=true)
     */
    private $preciosolespecial;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="subtotal", type="bigint", length=20)
     */
    private $subtotal;

    /**
     * @var integer
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="total", type="bigint", length=20)
     */
    private $total;

    /**
     * @var int
     *
     * @ORM\Column(name="entregado", type="smallint")
     */
    private $entregado;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidadpagado", type="bigint", nullable=true)
     */
    private $cantidadpagado;

    /**
     * @var int
     *
     * @ORM\Column(name="valordolar", type="bigint")
     */
    private $valordolar;

    /**
     * @var float
     *
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="totalusd", type="float")
     */
    private $totalusd;

    /**
     * @var int
     *
     * @ORM\Column(name="pagado", type="smallint")
     */
    private $pagado;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="registro_pago_completado", type="datetime_immutable", nullable=true)
     */
    private $registroPagoCompletado;

    /**
     * @Groups({"facturacion"})
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pago", mappedBy="tiendasolicitud",cascade={"persist","remove"})
     */
    private $pagos;

    public function __construct()
    {
        $this->pagos = new ArrayCollection();
        $this->producto = new ArrayCollection();
        $this->entregado = 2;
        $this->pagado = 2;
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

    public function getPeticionAsProducto()
    {
        return [
            'nombre' => $this->getSolicitudEspecial(),
            'precio' => $this->getPreciosolespecial(),
        ];
    }

    /**
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * @param int $folio
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Solicitud
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set solicitudEspecial
     *
     * @param string $solicitudEspecial
     *
     * @return Solicitud
     */
    public function setSolicitudEspecial($solicitudEspecial)
    {
        $this->solicitudEspecial = $solicitudEspecial;

        return $this;
    }

    /**
     * Get solicitudEspecial
     *
     * @return string
     */
    public function getSolicitudEspecial()
    {
        return $this->solicitudEspecial;
    }

    /**
     * Add producto
     *
     * @param Peticion $producto
     *
     * @return Solicitud
     */
    public function addProducto(Peticion $producto)
    {
        $producto->setSolicitud($this);
        $this->producto[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param Peticion $producto
     */
    public function removeProducto(Peticion $producto)
    {
        $this->producto->removeElement($producto);
    }

    /**
     * Get producto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set preciosolespecial
     *
     * @param integer $preciosolespecial
     *
     * @return Solicitud
     */
    public function setPreciosolespecial($preciosolespecial)
    {
        $this->preciosolespecial = $preciosolespecial;

        return $this;
    }

    /**
     * Get preciosolespecial
     *
     * @return integer
     */
    public function getPreciosolespecial()
    {
        return $this->preciosolespecial;
    }

    /**
     * Set nombrebarco
     *
     * @param \AppBundle\Entity\Barco $nombrebarco
     *
     * @return Solicitud
     */
    public function setNombrebarco(\AppBundle\Entity\Barco $nombrebarco = null)
    {
        $this->nombrebarco = $nombrebarco;

        return $this;
    }

    /**
     * Get nombrebarco
     *
     * @return \AppBundle\Entity\Barco
     */
    public function getNombrebarco()
    {
        return $this->nombrebarco;
    }

    /**
     * Set subtotal
     *
     * @param integer $subtotal
     *
     * @return Solicitud
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
     * @return Solicitud
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

    /**
     * Set entregado.
     *
     * @param int $entregado
     *
     * @return Solicitud
     */
    public function setEntregado($entregado)
    {
        $this->entregado = $entregado;

        return $this;
    }

    /**
     * Get entregado.
     *
     * @return int
     */
    public function getEntregado()
    {
        return $this->entregado;
    }

    /**
     * Set pagado.
     *
     * @param int $pagado
     *
     * @return Solicitud
     */
    public function setPagado($pagado)
    {
        $this->pagado = $pagado;

        return $this;
    }

    /**
     * Get pagado.
     *
     * @return int
     */
    public function getPagado()
    {
        return $this->pagado;
    }

    /**
     * Set registroPagoCompletado.
     *
     * @param \DateTimeImmutable|null $registroPagoCompletado
     *
     * @return Solicitud
     */
    public function setRegistroPagoCompletado($registroPagoCompletado = null)
    {
        $this->registroPagoCompletado = $registroPagoCompletado;

        return $this;
    }

    /**
     * Get registroPagoCompletado.
     *
     * @return \DateTimeImmutable|null
     */
    public function getRegistroPagoCompletado()
    {
        return $this->registroPagoCompletado;
    }

    /**
     * Add pago.
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return Solicitud
     */
    public function addPago(\AppBundle\Entity\Pago $pago)
    {
        $pago->setTiendasolicitud($this);
        $this->pagos[] = $pago;

        return $this;
    }

    /**
     * Remove pago.
     *
     * @param \AppBundle\Entity\Pago $pago
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePago(\AppBundle\Entity\Pago $pago)
    {
        return $this->pagos->removeElement($pago);
    }

    /**
     * Get pagos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagos()
    {
        return $this->pagos;
    }

    /**
     * Set cantidadpagado.
     *
     * @param int|null $cantidadpagado
     *
     * @return Solicitud
     */
    public function setCantidadpagado($cantidadpagado = null)
    {
        $this->cantidadpagado = $cantidadpagado;

        return $this;
    }

    /**
     * Get cantidadpagado.
     *
     * @return int|null
     */
    public function getCantidadpagado()
    {
        return $this->cantidadpagado;
    }

    /**
     * Set valordolar.
     *
     * @param int $valordolar
     *
     * @return Solicitud
     */
    public function setValordolar($valordolar)
    {
        $this->valordolar = $valordolar;

        return $this;
    }

    /**
     * Get valordolar.
     *
     * @return int
     */
    public function getValordolar()
    {
        return $this->valordolar;
    }

    /**
     * Set totalusd.
     *
     * @param float $totalusd
     *
     * @return Solicitud
     */
    public function setTotalusd($totalusd)
    {
        $this->totalusd = $totalusd;

        return $this;
    }

    /**
     * Get totalusd.
     *
     * @return float
     */
    public function getTotalusd()
    {
        return $this->totalusd;
    }

    /**
     * A falta de la relacion directa con el cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->getNombrebarco()->getCliente();
    }
    /**
     * Funcion falsa ya que solicitudes no se recotizan
     *
     * @return null
     */
    public function getFolioRecotiza()
    {
        return null;
    }
}

<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Compra\Concepto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Compra
 *
 * @ORM\Table(name="compra")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompraRepository")
 */
class Compra
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
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="folio", type="integer")
     */
    private $folio;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="validado", type="boolean", nullable=true)
     */
    private $validado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombreValido", type="string", length=255, nullable=true)
     */
    private $nombreValido;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="estatus", type="boolean", nullable=true)
     */
    private $estatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nota", type="text", nullable=true)
     */
    private $nota;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ivatotal", type="bigint", nullable=true)
     */
    private $ivatotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

    /**
     * @var Solicitud
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Solicitud")
     */
    private $solicitud;

    /**
     * @var Concepto
     *
     * @Assert\Valid()
     * @Assert\Count(
     *     min="1",
     *     minMessage="Debes incluir al menos un conepto para realizar una compra",
     * )
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Compra\Concepto", mappedBy="compra", cascade={"persist"})
     */
    private $conceptos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conceptos = new ArrayCollection();
        $this->validado = false;
        $this->estatus = true;
    }

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
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Compra
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
     * Set folio.
     *
     * @param int $folio
     *
     * @return Compra
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio.
     *
     * @return int
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set validado.
     *
     * @param bool|null $validado
     *
     * @return Compra
     */
    public function setValidado($validado = null)
    {
        $this->validado = $validado;

        return $this;
    }

    /**
     * Get validado.
     *
     * @return bool|null
     */
    public function getValidado()
    {
        return $this->validado;
    }

    /**
     * Set nombreValido.
     *
     * @param string|null $nombreValido
     *
     * @return Compra
     */
    public function setNombreValido($nombreValido = null)
    {
        $this->nombreValido = $nombreValido;

        return $this;
    }

    /**
     * Get nombreValido.
     *
     * @return string|null
     */
    public function getNombreValido()
    {
        return $this->nombreValido;
    }

    /**
     * Set estatus.
     *
     * @param bool|null $estatus
     *
     * @return Compra
     */
    public function setEstatus($estatus = null)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus.
     *
     * @return bool|null
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set nota.
     *
     * @param string|null $nota
     *
     * @return Compra
     */
    public function setNota($nota = null)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota.
     *
     * @return string|null
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set iva.
     *
     * @param float $iva
     *
     * @return Compra
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal.
     *
     * @param int|null $subtotal
     *
     * @return Compra
     */
    public function setSubtotal($subtotal = null)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int|null
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal.
     *
     * @param int|null $ivatotal
     *
     * @return Compra
     */
    public function setIvatotal($ivatotal = null)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal.
     *
     * @return int|null
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total.
     *
     * @param int|null $total
     *
     * @return Compra
     */
    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set creador.
     *
     * @param \AppBundle\Entity\Usuario|null $creador
     *
     * @return Compra
     */
    public function setCreador(\AppBundle\Entity\Usuario $creador = null)
    {
        $this->creador = $creador;

        return $this;
    }

    /**
     * Get creador.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * Set solicitud.
     *
     * @param \AppBundle\Entity\Solicitud|null $solicitud
     *
     * @return Compra
     */
    public function setSolicitud(\AppBundle\Entity\Solicitud $solicitud = null)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud.
     *
     * @return \AppBundle\Entity\Solicitud|null
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Add concepto.
     *
     * @param \AppBundle\Entity\Compra\Concepto $concepto
     *
     * @return Compra
     */
    public function addConcepto(\AppBundle\Entity\Compra\Concepto $concepto)
    {
        $concepto->setCompra($this);
        $this->conceptos[] = $concepto;

        return $this;
    }

    /**
     * Remove concepto.
     *
     * @param \AppBundle\Entity\Compra\Concepto $concepto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeConcepto(\AppBundle\Entity\Compra\Concepto $concepto)
    {
        return $this->conceptos->removeElement($concepto);
    }

    /**
     * Get conceptos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }
}

<?php

namespace AppBundle\Entity\Contabilidad;

use AppBundle\Entity\Contabilidad\Egreso\Entrada;
use AppBundle\Entity\Contabilidad\Egreso\Tipo;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Egreso
 *
 * @ORM\Table(name="contabilidad_egreso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\EgresoRepository")
 */
class Egreso
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
     * @var integer
     *
     * @ORM\Column(name="iva", type="bigint")
     */
    private $iva;

    /**
     * @var integer
     *
     * @ORM\Column(name="subtotal", type="bigint")
     */
    private $subtotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="ivatotal", type="bigint")
     */
    private $ivatotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario_editar", type="string", nullable=true)
     */
    private $comentarioEditar;

    /**
     * @var Emisor
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

    /**
     * @var Tipo
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Egreso\Tipo")
     */
    private $tipo;

    /**
     * @var Entrada
     *
     * @Assert\Valid
     * @Assert\Count(
     *     min="1",
     *     minMessage="Debe agregarse al menos una entrada"
     * )
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Contabilidad\Egreso\Entrada",
     *     mappedBy="egreso",
     *     cascade={"persist"}
     * )
     */
    private $entradas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entradas = new ArrayCollection();
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
     * @return Egreso
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
     * @param $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getComentarioEditar()
    {
        return $this->comentarioEditar;
    }

    /**
     * @param string $comentarioEditar
     */
    public function setComentarioEditar($comentarioEditar)
    {
        $this->comentarioEditar = $comentarioEditar;
    }

    /**
     * @return Emisor
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param Emisor $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * @return Tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param Tipo $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Add entrada.
     *
     * @param Entrada $entrada
     *
     * @return Egreso
     */
    public function addEntrada(Entrada $entrada)
    {
        $entrada->setEgreso($this);
        $this->entradas[] = $entrada;

        return $this;
    }

    /**
     * Remove entrada.
     *
     * @param Entrada $entrada
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEntrada(Entrada $entrada)
    {
        return $this->entradas->removeElement($entrada);
    }

    /**
     * Get entradas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntradas()
    {
        return $this->entradas;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }

    /**
     * Set iva.
     *
     * @param int $iva
     *
     * @return Egreso
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return int
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal.
     *
     * @param int $subtotal
     *
     * @return Egreso
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal.
     *
     * @param int $ivatotal
     *
     * @return Egreso
     */
    public function setIvatotal($ivatotal)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal.
     *
     * @return int
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }
}

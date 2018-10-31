<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Solicitud\Concepto;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Solicitud
 *
 * @ORM\Table(name="solicitud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SolicitudRepository")
 */
class Solicitud
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
     * @ORM\Column(name="folio", type="integer", length=255)
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
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @var string
     *
     * @ORM\Column(name="nota", type="text", nullable=true)
     */
    private $nota;

    /**
     * @var Emisor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

    /**
     * @var Concepto
     *
     * @Assert\Valid()
     * @Assert\Count(
     *     min="1",
     *     minMessage="Debes incluir al menos un conepto para realizar un registro",
     * )
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Solicitud\Concepto", mappedBy="solicitud", cascade={"persist"})
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

    public function __toString()
    {
        return $this->folio.'';
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
     * @return Solicitud
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
     * Set validado.
     *
     * @param bool|null $validado
     *
     * @return Solicitud
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
     * Set estatus.
     *
     * @param bool $estatus
     *
     * @return Solicitud
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus.
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set nombreValido.
     *
     * @param string|null $nombreValido
     *
     * @return Solicitud
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
     * Set empresa.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null $empresa
     *
     * @return Solicitud
     */
    public function setEmpresa(Emisor $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa.
     *
     * @return \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set creador.
     *
     * @param \AppBundle\Entity\Usuario|null $creador
     *
     * @return Solicitud
     */
    public function setCreador(Usuario $creador = null)
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
     * Add concepto.
     *
     * @param \AppBundle\Entity\Solicitud\Concepto $concepto
     *
     * @return Solicitud
     */
    public function addConcepto(Concepto $concepto)
    {
        $concepto->setSolicitud($this);
        $this->conceptos[] = $concepto;

        return $this;
    }

    /**
     * Remove concepto.
     *
     * @param \AppBundle\Entity\Solicitud\Concepto $concepto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeConcepto(\AppBundle\Entity\Solicitud\Concepto $concepto)
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

    /**
     * Set folio.
     *
     * @param int $folio
     *
     * @return Solicitud
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
     * Set nota.
     *
     * @param string|null $nota
     *
     * @return Solicitud
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
}

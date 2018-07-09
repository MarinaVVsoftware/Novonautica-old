<?php

namespace AppBundle\Entity\Contabilidad;

use AppBundle\Entity\Contabilidad\Egreso\Entrada;
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
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var Emisor
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

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
}

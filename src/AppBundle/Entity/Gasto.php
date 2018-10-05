<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use AppBundle\Entity\Gasto\Concepto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gasto
 *
 * @ORM\Table(name="gasto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GastoRepository")
 */
class Gasto
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
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var Emisor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

    /**
     * @var Concepto
     *
     * @Assert\Valid()
     * @Assert\Count(
     *     min="1",
     *     minMessage="Debes incluir al menos un conepto para realizar un registro",
     * )
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Gasto\Concepto", mappedBy="gasto", cascade={"persist"})
     */
    private $conceptos;

    public function __construct()
    {
        $this->conceptos = new ArrayCollection();
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
     * Set total.
     *
     * @param int $total
     *
     * @return Gasto
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Gasto
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
     * Set empresa.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null $empresa
     *
     * @return Gasto
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
     * Add concepto.
     *
     * @param \AppBundle\Entity\Gasto\Concepto $concepto
     *
     * @return Gasto
     */
    public function addConcepto(Concepto $concepto)
    {
        $concepto->setGasto($this);
        $this->conceptos[] = $concepto;

        return $this;
    }

    /**
     * Remove concepto.
     *
     * @param \AppBundle\Entity\Gasto\Concepto $concepto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeConcepto(\AppBundle\Entity\Gasto\Concepto $concepto)
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

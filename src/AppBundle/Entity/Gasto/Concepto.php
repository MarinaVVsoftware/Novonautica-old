<?php

namespace AppBundle\Entity\Gasto;

use AppBundle\Entity\Contabilidad\Catalogo\Servicio;
use AppBundle\Entity\Gasto;
use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="gasto_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Gasto\ConceptoRepository")
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
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var Servicio
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Catalogo\Servicio")
     */
    private $servicio;

    /**
     * @var Gasto
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Gasto", inversedBy="conceptos")
     */
    private $gasto;


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
     * @return Concepto
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
     * Set servicio.
     *
     * @param \AppBundle\Entity\Contabilidad\Catalogo\Servicio|null $servicio
     *
     * @return Concepto
     */
    public function setServicio(Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio.
     *
     * @return \AppBundle\Entity\Contabilidad\Catalogo\Servicio|null
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set gasto.
     *
     * @param \AppBundle\Entity\Gasto|null $gasto
     *
     * @return Concepto
     */
    public function setGasto(\AppBundle\Entity\Gasto $gasto = null)
    {
        $this->gasto = $gasto;

        return $this;
    }

    /**
     * Get gasto.
     *
     * @return \AppBundle\Entity\Gasto|null
     */
    public function getGasto()
    {
        return $this->gasto;
    }
}

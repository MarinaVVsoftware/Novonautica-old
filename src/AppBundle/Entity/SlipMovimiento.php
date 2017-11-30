<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlipMovimiento
 *
 * @ORM\Table(name="slip_movimiento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlipMovimientoRepository")
 */
class SlipMovimiento
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
     * @ORM\Column(name="fecha_llegada", type="date")
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_salida", type="date")
     */
    private $fechaSalida;

    /**
     * @var int
     *
     * @ORM\Column(name="estatus", type="smallint")
     */
    private $estatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Slip 0 = Desocupado, 1 = Ocupado
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Slip", inversedBy="movimientos")
     */
    private $slip;

    public function __construct()
    {
        $this->setEstatus(0);
        $this->setCreatedAt(new \DateTime('now'));
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

    /**
     * Set fechaLlegada
     *
     * @param \DateTime $fechaLlegada
     *
     * @return SlipMovimiento
     */
    public function setFechaLlegada($fechaLlegada)
    {
        $this->fechaLlegada = $fechaLlegada;

        return $this;
    }

    /**
     * Get fechaLlegada
     *
     * @return \DateTime
     */
    public function getFechaLlegada()
    {
        return $this->fechaLlegada;
    }

    /**
     * Set fechaSalida
     *
     * @param \DateTime $fechaSalida
     *
     * @return SlipMovimiento
     */
    public function setFechaSalida($fechaSalida)
    {
        $this->fechaSalida = $fechaSalida;

        return $this;
    }

    /**
     * Get fechaSalida
     *
     * @return \DateTime
     */
    public function getFechaSalida()
    {
        return $this->fechaSalida;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     *
     * @return SlipMovimiento
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return int
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SlipMovimiento
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set slip
     *
     * @param Slip $slip
     *
     * @return SlipMovimiento
     */
    public function setSlip(Slip $slip = null)
    {
        $this->slip = $slip;

        return $this;
    }

    /**
     * Get slip
     *
     * @return Slip
     */
    public function getSlip()
    {
        return $this->slip;
    }
}

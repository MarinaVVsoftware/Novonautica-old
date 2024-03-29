<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SlipMovimiento
 *
 * @ORM\Table(name="slip_movimiento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlipMovimientoRepository")
 * @UniqueEntity(
 *     fields={"marinahumedacotizacion"},
 *     errorPath="port",
 *     message="Esta cotización ya ha sido asignada a otro slip."
 * )
 */
class SlipMovimiento
{
    /**
     * @var int
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\Column(name="fecha_llegada", type="datetime")
     */
    private $fechaLlegada;

    /**
     * @var \DateTime
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\Column(name="fecha_salida", type="datetime")
     */
    private $fechaSalida;

    /**
     * @var string
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\Column(name="nota", type="string", nullable=true)
     */
    private $nota;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\OneToOne(targetEntity="MarinaHumedaCotizacion", inversedBy="slipmovimiento")
     * @ORM\JoinColumn(name="idmarinahumedacotizacion", referencedColumnName="id")
     */
    private $marinahumedacotizacion;


    /**
     * @var Slip 0 = Desocupado, 1 = Ocupado
     *
     * @Groups({"currentOcupation"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Slip", inversedBy="movimientos")
     */
    private $slip;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function __toString()
    {
        return 'hello world';
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

    /**
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * @param string $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * Set marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return SlipMovimiento
     */
    public function setMarinahumedacotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion = null)
    {
        $this->marinahumedacotizacion = $marinahumedacotizacion;

        return $this;
    }

    /**
     * Get marinahumedacotizacion
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion
     */
    public function getMarinahumedacotizacion()
    {
        return $this->marinahumedacotizacion;
    }
}

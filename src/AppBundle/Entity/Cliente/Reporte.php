<?php

namespace AppBundle\Entity\Cliente;

use AppBundle\Entity\Cliente;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reporte
 *
 * @ORM\Table(name="cliente_reporte")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cliente\ReporteRepository")
 */
class Reporte
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
     * @ORM\Column(name="adeudo", type="float")
     */
    private $adeudo;

    /**
     * @var float
     *
     * @ORM\Column(name="abono", type="float")
     */
    private $abono;

    /**
     * @var string
     *
     * @ORM\Column(name="concepto", type="string", length=50)
     */
    private $concepto;

    /**
     * @var string
     *
     * @ORM\Column(name="referencia", type="string", length=20)
     */
    private $referencia;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="reportes")
     */
    private $cliente;

    public function __construct()
    {
        $this->adeudo = 0;
        $this->abono = 0;
        $this->createdAt = new \DateTimeImmutable();
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
     * Set adeudo.
     *
     * @param float $adeudo
     *
     * @return Reporte
     */
    public function setAdeudo($adeudo)
    {
        $this->adeudo = $adeudo;

        return $this;
    }

    /**
     * Get adeudo.
     *
     * @return float
     */
    public function getAdeudo()
    {
        return $this->adeudo;
    }

    /**
     * Set abono.
     *
     * @param float $abono
     *
     * @return Reporte
     */
    public function setAbono($abono)
    {
        $this->abono = $abono;

        return $this;
    }

    /**
     * Get abono.
     *
     * @return float
     */
    public function getAbono()
    {
        return $this->abono;
    }

    /**
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * @param string $concepto
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;
    }

    /**
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @param string $referencia
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTimeImmutable $createdAt
     *
     * @return Reporte
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set cliente.
     *
     * @param Cliente|null $cliente
     *
     * @return Reporte
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente.
     *
     * @return Cliente|null
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}

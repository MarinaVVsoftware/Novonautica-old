<?php

namespace AppBundle\Entity\Astillero\Contratista;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pago
 *
 * @ORM\Table(name="astillero_contratista_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Contratista\PagoRepository")
 */
class Pago
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
     * @ORM\Column(name="cantidad", type="bigint")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="forma", type="string", length=50, nullable=true)
     */
    private $forma;

    /**
     * @var int
     *
     * @ORM\Column(name="saldo", type="bigint")
     */
    private $saldo;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Contratista", inversedBy="contratistapagos")
     * @ORM\JoinColumn(name="idcontratista", referencedColumnName="id",onDelete="CASCADE")
     */
    private $contratista;

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
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return Pago
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
    /**
     * Set divisa.
     *
     * @param string $divisa
     *
     * @return Pago
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Pago
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
     * Set forma.
     *
     * @param string|null $forma
     *
     * @return Pago
     */
    public function setForma($forma = null)
    {
        $this->forma = $forma;

        return $this;
    }

    /**
     * Get forma.
     *
     * @return string|null
     */
    public function getForma()
    {
        return $this->forma;
    }

    /**
     * Set saldo.
     *
     * @param int $saldo
     *
     * @return Pago
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo.
     *
     * @return int
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set contratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista|null $contratista
     *
     * @return Pago
     */
    public function setContratista(\AppBundle\Entity\Astillero\Contratista $contratista = null)
    {
        $this->contratista = $contratista;

        return $this;
    }

    /**
     * Get contratista.
     *
     * @return \AppBundle\Entity\Astillero\Contratista|null
     */
    public function getContratista()
    {
        return $this->contratista;
    }

}

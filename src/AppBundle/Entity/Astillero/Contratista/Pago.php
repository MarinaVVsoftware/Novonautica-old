<?php

namespace AppBundle\Entity\Astillero\Contratista;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Pago
 *
 * @ORM\Table(name="astillero_contratista_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Contratista\PagoRepository")
 */
class Pago
{
    const PAGO_EFECTIVO = 1;
    const PAGO_TRANSFERENCIA = 2;
    const PAGO_TARJETA_CREDITO = 3;
    const PAGO_TARJETA_DEBITO = 4;

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
     * @Groups({"AstilleroReporte"})
     *
     * @ORM\Column(name="cantidad", type="bigint")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @Groups({"AstilleroReporte"})
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var \DateTime
     *
     * @Groups({"AstilleroReporte"})
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="forma", type="smallint")
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

    private static $formaList = [
        Pago::PAGO_EFECTIVO => 'Efectivo',
        Pago::PAGO_TRANSFERENCIA => 'Transferencia',
        Pago::PAGO_TARJETA_CREDITO => 'Tarjeta de crédito',
        Pago::PAGO_TARJETA_DEBITO => 'Tarjeta de débito'
     ];

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
     * @param int $forma
     */
    public function setForma($forma)
    {
        $this->forma = $forma;
    }

    /**
     * Get forma.
     *
     * @return int
     */
    public function getForma()
    {
        if (null === $this->forma){ return null; }
        return $this->forma;
    }

    /**
     * @return int
     */
    public function getFormaNombre()
    {
        if (null === $this->forma) { return null; }
        return self::$formaList[$this->forma];
    }

    public static function getFormaList(){
        return self::$formaList;
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

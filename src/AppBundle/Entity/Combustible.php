<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Combustible
 *
 * @ORM\Table(name="combustible")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CombustibleRepository")
 */
class Combustible
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
     * @ORM\Column(name="folio", type="integer")
     */
    private $folio;

    /**
     * @var int|null
     *
     * @ORM\Column(name="foliorecotiza", type="integer", nullable=true)
     */
    private $foliorecotiza;

    /**
     * @var float
     *
     * @ORM\Column(name="dolar", type="float")
     */
    private $dolar;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subtotal", type="bigint", nullable=true)
     */
    private $subtotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ivatotal", type="bigint", nullable=true)
     */
    private $ivatotal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total", type="bigint", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;


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
     * Set folio.
     *
     * @param int $folio
     *
     * @return Combustible
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
     * Set foliorecotiza.
     *
     * @param int|null $foliorecotiza
     *
     * @return Combustible
     */
    public function setFoliorecotiza($foliorecotiza = null)
    {
        $this->foliorecotiza = $foliorecotiza;

        return $this;
    }

    /**
     * Get foliorecotiza.
     *
     * @return int|null
     */
    public function getFoliorecotiza()
    {
        return $this->foliorecotiza;
    }

    /**
     * Set dolar.
     *
     * @param float $dolar
     *
     * @return Combustible
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;

        return $this;
    }

    /**
     * Get dolar.
     *
     * @return float
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * Set iva.
     *
     * @param float $iva
     *
     * @return Combustible
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva.
     *
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set subtotal.
     *
     * @param int|null $subtotal
     *
     * @return Combustible
     */
    public function setSubtotal($subtotal = null)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return int|null
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set ivatotal.
     *
     * @param int|null $ivatotal
     *
     * @return Combustible
     */
    public function setIvatotal($ivatotal = null)
    {
        $this->ivatotal = $ivatotal;

        return $this;
    }

    /**
     * Get ivatotal.
     *
     * @return int|null
     */
    public function getIvatotal()
    {
        return $this->ivatotal;
    }

    /**
     * Set total.
     *
     * @param int|null $total
     *
     * @return Combustible
     */
    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return Combustible
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
}

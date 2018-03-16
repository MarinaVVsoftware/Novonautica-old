<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contratista
 *
 * @ORM\Table(name="astillero_contratista")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ContratistaRepository")
 */
class Contratista
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
     * @var string
     *
     * @ORM\Column(name="cotizacionInicial", type="string", length=255)
     */
    private $cotizacionInicial;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="ivatot", type="bigint")
     */
    private $ivatot;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;


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
     * Set cotizacionInicial.
     *
     * @param string $cotizacionInicial
     *
     * @return Contratista
     */
    public function setCotizacionInicial($cotizacionInicial)
    {
        $this->cotizacionInicial = $cotizacionInicial;

        return $this;
    }

    /**
     * Get cotizacionInicial.
     *
     * @return string
     */
    public function getCotizacionInicial()
    {
        return $this->cotizacionInicial;
    }

    /**
     * Set precio.
     *
     * @param int $precio
     *
     * @return Contratista
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set iva.
     *
     * @param float $iva
     *
     * @return Contratista
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
     * Set ivatot.
     *
     * @param int $ivatot
     *
     * @return Contratista
     */
    public function setIvatot($ivatot)
    {
        $this->ivatot = $ivatot;

        return $this;
    }

    /**
     * Get ivatot.
     *
     * @return int
     */
    public function getIvatot()
    {
        return $this->ivatot;
    }

    /**
     * Set total.
     *
     * @param int $total
     *
     * @return Contratista
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
}

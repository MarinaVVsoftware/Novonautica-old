<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarinaHumedaTarifa
 *
 * @ORM\Table(name="marina_humeda_tarifa")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaTarifaRepository")
 */
class MarinaHumedaTarifa
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
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;

    /**
     * @var float
     *
     * @ORM\Column(name="costo", type="float")
     */
    private $costo;

    /**
     * @var float
     *
     * @ORM\Column(name="pies", type="float", nullable=true)
     */
    private $pies;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    public function __toString()
    {
        return '$'.$this->costo.' - '.$this->descripcion;
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
     * Set tipo
     *
     * @param integer $tipo
     *
     * @return MarinaHumedaTarifa
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set costo
     *
     * @param float $costo
     *
     * @return MarinaHumedaTarifa
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;

        return $this;
    }

    /**
     * Get costo
     *
     * @return float
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * Set pies
     *
     * @param float $pies
     *
     * @return MarinaHumedaTarifa
     */
    public function setPies($pies)
    {
        $this->pies = $pies;

        return $this;
    }

    /**
     * Get pies
     *
     * @return float
     */
    public function getPies()
    {
        return $this->pies;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return MarinaHumedaTarifa
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
}


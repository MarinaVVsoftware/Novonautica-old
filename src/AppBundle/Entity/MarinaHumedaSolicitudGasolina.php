<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarinaHumedaSolicitudGasolina
 *
 * @ORM\Table(name="marina_humeda_solicitud_gasolina")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaSolicitudGasolinaRepository")
 */
class MarinaHumedaSolicitudGasolina
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="gasolinabarco")
     */
    private $idbarco;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad_combustible", type="integer")
     */
    private $cantidadCombustible;

    /**
     * @var datetime_immutable
     *
     * @ORM\Column(name="fecha_peticion", type="datetime_immutable")
     */
    private $fechaPeticion;


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
     * Set idbarco.
     *
     * @param int $idbarco
     *
     * @return MarinaHumedaSolicitudGasolina
     */
    public function setIdbarco($idbarco)
    {
        $this->idbarco = $idbarco;

        return $this;
    }

    /**
     * Get idbarco.
     *
     * @return int
     */
    public function getIdbarco()
    {
        return $this->idbarco;
    }

    /**
     * Set cantidadCombustible.
     *
     * @param int $cantidadCombustible
     *
     * @return MarinaHumedaSolicitudGasolina
     */
    public function setCantidadCombustible($cantidadCombustible)
    {
        $this->cantidadCombustible = $cantidadCombustible;

        return $this;
    }

    /**
     * Get cantidadCombustible.
     *
     * @return int
     */
    public function getCantidadCombustible()
    {
        return $this->cantidadCombustible;
    }

    /**
     * Set fechaPeticion.
     *
     * @param datetime_immutable $fechaPeticion
     *
     * @return MarinaHumedaSolicitudGasolina
     */
    public function setFechaPeticion($fechaPeticion)
    {
        $this->fechaPeticion = $fechaPeticion;

        return $this;
    }

    /**
     * Get fechaPeticion.
     *
     * @return datetime_immutable
     */
    public function getFechaPeticion()
    {
        return $this->fechaPeticion;
    }
}

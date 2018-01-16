<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitud
 *
 * @ORM\Table(name="tienda_solicitud")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\SolicitudRepository")
 */
class Solicitud
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
     * @var datetime_immutable
     *
     * @ORM\Column(name="fecha", type="datetime_immutable")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="embarcacion", type="string", length=150)
     */
    private $embarcacion;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud", type="string", length=100)
     */
    private $solicitud;

    /**
     * @var string
     *
     * @ORM\Column(name="solicitud_especial", type="string", length=150)
     */
    private $solicitudEspecial;


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
     * Set fecha
     *
     * @param datetime_immutable $fecha
     *
     * @return Solicitud
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return datetime_immutable
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set embarcacion
     *
     * @param string $embarcacion
     *
     * @return Solicitud
     */
    public function setEmbarcacion($embarcacion)
    {
        $this->embarcacion = $embarcacion;

        return $this;
    }

    /**
     * Get embarcacion
     *
     * @return string
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }

    /**
     * Set solicitud
     *
     * @param string $solicitud
     *
     * @return Solicitud
     */
    public function setSolicitud($solicitud)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud
     *
     * @return string
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set solicitudEspecial
     *
     * @param string $solicitudEspecial
     *
     * @return Solicitud
     */
    public function setSolicitudEspecial($solicitudEspecial)
    {
        $this->solicitudEspecial = $solicitudEspecial;

        return $this;
    }

    /**
     * Get solicitudEspecial
     *
     * @return string
     */
    public function getSolicitudEspecial()
    {
        return $this->solicitudEspecial;
    }
}


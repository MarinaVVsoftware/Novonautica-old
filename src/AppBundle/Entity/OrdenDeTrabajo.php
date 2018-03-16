<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenDeTrabajo
 *
 * @ORM\Table(name="orden_de_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenDeTrabajoRepository")
 */
class OrdenDeTrabajo
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
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\AstilleroCotizacion", inversedBy="odt")
     * @ORM\JoinColumn(name="idastillerocotizacion", referencedColumnName="id")
     */
    private $astilleroCotizacion;



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
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return OrdenDeTrabajo
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set astilleroCotizacion
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astilleroCotizacion
     *
     * @return OrdenDeTrabajo
     */
    public function setAstilleroCotizacion(\AppBundle\Entity\AstilleroCotizacion $astilleroCotizacion = null)
    {
        $this->astilleroCotizacion = $astilleroCotizacion;

        return $this;
    }

    /**
     * Get astilleroCotizacion
     *
     * @return \AppBundle\Entity\AstilleroCotizacion
     */
    public function getAstilleroCotizacion()
    {
        return $this->astilleroCotizacion;
    }
}

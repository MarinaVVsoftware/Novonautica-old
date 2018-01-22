<?php

namespace AppBundle\Entity\Tienda;

use Doctrine\ORM\Mapping as ORM;

/**
 * Peticion
 *
 * @ORM\Table(name="tienda_peticion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\PeticionRepository")
 */
class Peticion
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
     * @ORM\Column(name="peticion", type="string", length=255)
     */
    private $peticion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Solicitud", inversedBy="producto")
     * @ORM\JoinColumn(name="idpeticion", referencedColumnName="id",onDelete="CASCADE")
     */
    private $solicitud;

    /**
     * @var integer
     * @ORM\Column(name="cantidad", type="integer", length=255)
     */
    private $cantidad;

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
     * Set peticion
     *
     * @param string $peticion
     *
     * @return Peticion
     */
    public function setPeticion($peticion)
    {
        $this->peticion = $peticion;

        return $this;
    }

    /**
     * Get peticion
     *
     * @return string
     */
    public function getPeticion()
    {
        return $this->peticion;
    }

    /**
     * Set solicitud
     *
     * @param Solicitud $solicitud
     *
     * @return Peticion
     */
    public function setSolicitud(Solicitud $solicitud = null)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud
     *
     * @return Solicitud
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return Peticion
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
}

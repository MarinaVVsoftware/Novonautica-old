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
     * @ORM\JoinColumn(name="idproducto", referencedColumnName="id",onDelete="CASCADE")
     */
    private $solicitud;

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
     * @param \AppBundle\Entity\Tienda\Solicitud $solicitud
     *
     * @return Peticion
     */
    public function setSolicitud(\AppBundle\Entity\Tienda\Solicitud $solicitud = null)
    {
        $this->solicitud = $solicitud;

        return $this;
    }

    /**
     * Get solicitud
     *
     * @return \AppBundle\Entity\Tienda\Solicitud
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }
}

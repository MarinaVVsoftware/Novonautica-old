<?php

namespace AppBundle\Entity\Astillero\Proveedor;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trabajo
 *
 * @ORM\Table(name="astillero_proveedor_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Proveedor\TrabajoRepository")
 */
class Trabajo
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
     * @Assert\NotBlank(message="Escríba el nombre del tipo de trabajo")
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    public function __toString()
    {
        return $this->nombre;
    }

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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Trabajo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

}

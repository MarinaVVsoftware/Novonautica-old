<?php

namespace AppBundle\Entity\Astillero\Contratista\Actividad;

use AppBundle\Entity\Astillero\Contratista\Actividad;
use Doctrine\ORM\Mapping as ORM;

/**
 * Foto
 *
 * @ORM\Table(name="astillero_contratista_actividad_foto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Contratista\Actividad\FotoRepository")
 * @ORM\EntityListeners({"FotoListener"})
 */
class Foto
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
     * @ORM\Column(name="basename", type="string", length=255)
     */
    private $basename;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var Actividad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Contratista\Actividad", inversedBy="fotos")
     */
    private $actividad;

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
     * Set basename.
     *
     * @param string $basename
     *
     * @return Foto
     */
    public function setBasename($basename)
    {
        $this->basename = $basename;

        return $this;
    }

    /**
     * Get basename.
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * Set size.
     *
     * @param int $size
     *
     * @return Foto
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set actividad.
     *
     * @param Actividad|null $actividad
     *
     * @return Foto
     */
    public function setActividad(Actividad $actividad = null)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad.
     *
     * @return Actividad|null
     */
    public function getActividad()
    {
        return $this->actividad;
    }
}

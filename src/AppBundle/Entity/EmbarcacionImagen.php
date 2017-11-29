<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmbarcacionImagen
 *
 * @ORM\Table(name="embarcacion_imagen")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionImagenRepository")
 */
class EmbarcacionImagen
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
     * @var int Tipos 1 = Imagen, 2 = Layout
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    /**
     * @var Embarcacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Embarcacion", inversedBy="imagenes")
     */
    private $embarcacion;

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
     * Set basename
     *
     * @param string $basename
     *
     * @return EmbarcacionImagen
     */
    public function setBasename($basename)
    {
        $this->basename = $basename;

        return $this;
    }

    /**
     * Get basename
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     *
     * @return EmbarcacionImagen
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set embarcacion
     *
     * @param Embarcacion $embarcacion
     *
     * @return EmbarcacionImagen
     */
    public function setEmbarcacion(Embarcacion $embarcacion = null)
    {
        $this->embarcacion = $embarcacion;

        return $this;
    }

    /**
     * Get embarcacion
     *
     * @return Embarcacion
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }
}

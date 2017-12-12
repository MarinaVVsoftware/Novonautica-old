<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmbarcacionLayout
 *
 * @ORM\Table(name="embarcacion_layout")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionLayoutRepository")
 */
class EmbarcacionLayout
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
     * @var Embarcacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Embarcacion", inversedBy="layouts")
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
     * @return EmbarcacionLayout
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
     * Set size
     *
     * @param integer $size
     *
     * @return EmbarcacionLayout
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return Embarcacion
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }

    /**
     * @param Embarcacion $embarcacion
     */
    public function setEmbarcacion($embarcacion)
    {
        $this->embarcacion = $embarcacion;
    }
}


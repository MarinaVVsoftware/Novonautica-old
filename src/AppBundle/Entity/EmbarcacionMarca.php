<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmbarcacionMarca
 *
 * @ORM\Table(name="embarcacion_marca")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionMarcaRepository")
 */
class EmbarcacionMarca
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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var EmbarcacionModelo
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EmbarcacionModelo", mappedBy="marca", cascade={"remove"})
     */
    private $modelos;

    public function __construct()
    {
        $this->modelos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNombre();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return EmbarcacionMarca
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add modelo
     *
     * @param EmbarcacionModelo $modelo
     *
     * @return EmbarcacionMarca
     */
    public function addModelo(EmbarcacionModelo $modelo)
    {
        $this->modelos[] = $modelo;

        return $this;
    }

    /**
     * Remove modelo
     *
     * @param EmbarcacionModelo $modelo
     */
    public function removeModelo(EmbarcacionModelo $modelo)
    {
        $this->modelos->removeElement($modelo);
    }

    /**
     * Get modelos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelos()
    {
        return $this->modelos;
    }
}

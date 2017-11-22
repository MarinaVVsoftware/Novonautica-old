<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmbarcacionModelo
 *
 * @ORM\Table(name="embarcacion_modelo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionModeloRepository")
 */
class EmbarcacionModelo
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
     * @var EmbarcacionMarca
     *
     * @Assert\NotBlank(message="Elige una opción")
     * @Assert\NotNull(message="Elige una opción")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmbarcacionMarca", inversedBy="modelos")
     *
     * @MaxDepth(1)
     * @Groups({"marca"})
     */
    private $marca;

    public function __toString()
    {
        return $this->nombre;
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
     * @return EmbarcacionModelo
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
     * Set marca
     *
     * @param EmbarcacionMarca $marca
     *
     * @return EmbarcacionModelo
     */
    public function setMarca(EmbarcacionMarca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return EmbarcacionMarca
     */
    public function getMarca()
    {
        return $this->marca;
    }
}

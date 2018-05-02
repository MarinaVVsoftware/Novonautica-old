<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Servicio
 *
 * @ORM\Table(name="astillero_servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ServicioRepository")
 */
class Servicio
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
     * @Groups({"facturacion"})
     * @Assert\NotBlank(
     *     message="Nombre no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var int
     * @Assert\NotBlank(
     *     message="Precio no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="precio", type="bigint", nullable=true)
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Unidad no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="unidad", type="string", length=20, nullable=true)
     */
    private $unidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

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
     * @return Servicio
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
     * Set precio
     *
     * @param integer $precio
     *
     * @return Servicio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     *
     * @return Servicio
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Servicio
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set divisa.
     *
     * @param string $divisa
     *
     * @return Servicio
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }
}

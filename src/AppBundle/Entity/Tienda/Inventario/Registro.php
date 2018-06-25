<?php

namespace AppBundle\Entity\Tienda\Inventario;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registro
 *
 * @ORM\Table(name="tienda_inventario_registro")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\Inventario\RegistroRepository")
 */
class Registro
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referencia", type="string", length=50, nullable=true)
     */
    private $referencia;


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
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Registro
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set referencia.
     *
     * @param string|null $referencia
     *
     * @return Registro
     */
    public function setReferencia($referencia = null)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia.
     *
     * @return string|null
     */
    public function getReferencia()
    {
        return $this->referencia;
    }
}

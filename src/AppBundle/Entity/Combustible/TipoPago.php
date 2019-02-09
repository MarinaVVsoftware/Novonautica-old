<?php

namespace AppBundle\Entity\Combustible;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPago
 *
 * @ORM\Table(name="combustible_tipo_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Combustible\TipoPagoRepository")
 */
class TipoPago
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
     * @var int
     *
     * @ORM\Column(name="porcentaje", type="integer")
     */
    private $porcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=30)
     */
    private $nombre;

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
     * Set porcentaje.
     *
     * @param int $porcentaje
     *
     * @return TipoPago
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje.
     *
     * @return int
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return TipoPago
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

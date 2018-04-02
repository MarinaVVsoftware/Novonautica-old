<?php

namespace AppBundle\Entity\Astillero\Proveedor;

use Doctrine\ORM\Mapping as ORM;

/**
 * CatalogoTrabajo
 *
 * @ORM\Table(name="astillero_proveedor_catalogo_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Proveedor\CatalogoTrabajoRepository")
 */
class CatalogoTrabajo
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
     * @ORM\Column(name="nombre", type="string", length=255)
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return CatalogoTrabajo
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

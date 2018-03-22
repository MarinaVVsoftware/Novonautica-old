<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proveedor
 *
 * @ORM\Table(name="astillero_proveedor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ProveedorRepository")
 */
class Proveedor
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
     * @Assert\NotBlank(
     *     message="Nombre no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Razón social no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="razonsocial", type="string", length=255)
     */
    private $razonsocial;

    /**
     * @var float
     * @Assert\NotBlank(
     *     message="Porcentaje no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="porcentaje", type="float")
     */
    private $porcentaje;

    /**
     * @var int Estatus: 0 Externo, 1 Interno
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Astillero\Contratista", mappedBy="proveedor")
     */
    private $AContratistas;

    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->AContratistas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Proveedor
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

    /**
     * Set razonsocial.
     *
     * @param string $razonsocial
     *
     * @return Proveedor
     */
    public function setRazonsocial($razonsocial)
    {
        $this->razonsocial = $razonsocial;

        return $this;
    }

    /**
     * Get razonsocial.
     *
     * @return string
     */
    public function getRazonsocial()
    {
        return $this->razonsocial;
    }

    /**
     * Set porcentaje.
     *
     * @param float $porcentaje
     *
     * @return Proveedor
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje.
     *
     * @return float
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     * @return Proveedor
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Add aContratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista $aContratista
     *
     * @return Proveedor
     */
    public function addAContratista(\AppBundle\Entity\Astillero\Contratista $aContratista)
    {
        $this->AContratistas[] = $aContratista;

        return $this;
    }

    /**
     * Remove aContratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista $aContratista
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAContratista(\AppBundle\Entity\Astillero\Contratista $aContratista)
    {
        return $this->AContratistas->removeElement($aContratista);
    }

    /**
     * Get aContratistas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAContratistas()
    {
        return $this->AContratistas;
    }
}

<?php

namespace AppBundle\Entity\Astillero\Contratista;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actividad
 *
 * @ORM\Table(name="astillero_contratista_actividad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Contratista\ActividadRepository")
 */
class Actividad
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
     * @var \DateTime
     *
     * @ORM\Column(name="inicio", type="datetime")
     */
    private $inicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="datetime")
     */
    private $fin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notas", type="text", nullable=true)
     */
    private $notas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="usuario", type="string", length=50, nullable=true)
     */
    private $usuario;

    /**
     * @var float|null
     *
     * @ORM\Column(name="porcentaje", type="float", nullable=true)
     */
    private $porcentaje;

    /**
     * @var string|null
     *
     * @ORM\Column(name="responsable", type="string", length=70, nullable=true)
     */
    private $responsable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Contratista", inversedBy="contratistaactividades")
     * @ORM\JoinColumn(name="idcontratista", referencedColumnName="id",onDelete="CASCADE")
     */
    private $contratista;

    public function __construct()
    {
        $this->porcentaje = 0;
        $this->fecha = new \DateTime('now');
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
     * @return Actividad
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
     * Set inicio.
     *
     * @param \DateTime $inicio
     *
     * @return Actividad
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;

        return $this;
    }

    /**
     * Get inicio.
     *
     * @return \DateTime
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * Set fin.
     *
     * @param \DateTime $fin
     *
     * @return Actividad
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin.
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set notas.
     *
     * @param string|null $notas
     *
     * @return Actividad
     */
    public function setNotas($notas = null)
    {
        $this->notas = $notas;

        return $this;
    }

    /**
     * Get notas.
     *
     * @return string|null
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Actividad
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
     * Set usuario.
     *
     * @param string|null $usuario
     *
     * @return Actividad
     */
    public function setUsuario($usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return string|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set porcentaje.
     *
     * @param float|null $porcentaje
     *
     * @return Actividad
     */
    public function setPorcentaje($porcentaje = null)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje.
     *
     * @return float|null
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set contratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista|null $contratista
     *
     * @return Actividad
     */
    public function setContratista(\AppBundle\Entity\Astillero\Contratista $contratista = null)
    {
        $this->contratista = $contratista;

        return $this;
    }

    /**
     * Get contratista.
     *
     * @return \AppBundle\Entity\Astillero\Contratista|null
     */
    public function getContratista()
    {
        return $this->contratista;
    }

    /**
     * Set porcentae.
     *
     * @param float|null $porcentae
     *
     * @return Actividad
     */
    public function setPorcentae($porcentae = null)
    {
        $this->porcentae = $porcentae;

        return $this;
    }

    /**
     * Get porcentae.
     *
     * @return float|null
     */
    public function getPorcentae()
    {
        return $this->porcentae;
    }

    /**
     * Set responsable.
     *
     * @param string|null $responsable
     *
     * @return Actividad
     */
    public function setResponsable($responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable.
     *
     * @return string|null
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evento
 *
 * @ORM\Table(name="evento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventoRepository")
 */
class Evento
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
     *     message="Título no pude quedar vacío"
     * )
     *
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Fecha inicio no puede quedar vacío"
     * )
     * @Assert\Date()
     *
     * @ORM\Column(name="fechainicio", type="datetime")
     */
    private $fechainicio;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Fecha fin no puede quedar vacío"
     * )
     * @Assert\Date()
     *
     * @ORM\Column(name="fechafin", type="datetime", nullable=true)
     */
    private $fechafin;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Hora inicio no puede quedar vacío"
     * )
     *  @Assert\Time()
     *
     * @ORM\Column(name="horainicio", type="time", nullable=true)
     */
    private $horainicio;

    /**
     * @var \DateTime
     * @Assert\NotBlank(
     *     message="Hora fin no puede quedar vacío"
     * )
     *  @Assert\Time()
     *
     * @ORM\Column(name="horafin", type="time", nullable=true)
     */
    private $horafin;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="fondocolor", type="string", length=7)
     */
    private $fondocolor;

    /**
     * @var string
     *
     * @ORM\Column(name="letracolor", type="string", length=7)
     */
    private $letracolor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_publico", type="boolean")
     */
    private $isPublico;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime")
     */
    private $fecharegistro;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="idusuario", referencedColumnName="id")
     */
    private $usuario;

    public function __construct()
    {
        $this->setFecharegistro(new \DateTime('now'));
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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Evento
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set fechainicio
     *
     * @param \DateTime $fechainicio
     *
     * @return Evento
     */
    public function setFechainicio($fechainicio)
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    /**
     * Get fechainicio
     *
     * @return \DateTime
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * Set fechafin
     *
     * @param \DateTime $fechafin
     *
     * @return Evento
     */
    public function setFechafin($fechafin)
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    /**
     * Get fechafin
     *
     * @return \DateTime
     */
    public function getFechafin()
    {
        return $this->fechafin;
    }

    /**
     * Set horainicio
     *
     * @param \DateTime $horainicio
     *
     * @return Evento
     */
    public function setHorainicio($horainicio)
    {
        $this->horainicio = $horainicio;

        return $this;
    }

    /**
     * Get horainicio
     *
     * @return \DateTime
     */
    public function getHorainicio()
    {
        return $this->horainicio;
    }

    /**
     * Set horafin
     *
     * @param \DateTime $horafin
     *
     * @return Evento
     */
    public function setHorafin($horafin)
    {
        $this->horafin = $horafin;

        return $this;
    }

    /**
     * Get horafin
     *
     * @return \DateTime
     */
    public function getHorafin()
    {
        return $this->horafin;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Evento
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
     * Set fondocolor.
     *
     * @param string $fondocolor
     *
     * @return Evento
     */
    public function setFondocolor($fondocolor)
    {
        $this->fondocolor = $fondocolor;

        return $this;
    }

    /**
     * Get fondocolor.
     *
     * @return string
     */
    public function getFondocolor()
    {
        return $this->fondocolor;
    }

    /**
     * Set letracolor.
     *
     * @param string $letracolor
     *
     * @return Evento
     */
    public function setLetracolor($letracolor)
    {
        $this->letracolor = $letracolor;

        return $this;
    }

    /**
     * Get letracolor.
     *
     * @return string
     */
    public function getLetracolor()
    {
        return $this->letracolor;
    }

    /**
     * Set isPublico.
     *
     * @param bool $isPublico
     *
     * @return Evento
     */
    public function setIsPublico($isPublico)
    {
        $this->isPublico = $isPublico;

        return $this;
    }

    /**
     * Get isPublico.
     *
     * @return bool
     */
    public function getIsPublico()
    {
        return $this->isPublico;
    }

    /**
     * Set usuario.
     *
     * @param \AppBundle\Entity\Usuario|null $usuario
     *
     * @return Evento
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set fecharegistro.
     *
     * @param \DateTime $fecharegistro
     *
     * @return Evento
     */
    public function setFecharegistro($fecharegistro)
    {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    /**
     * Get fecharegistro.
     *
     * @return \DateTime
     */
    public function getFecharegistro()
    {
        return $this->fecharegistro;
    }
}

<?php

namespace AppBundle\Entity\Astillero\Contratista\Actividad;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pausa
 *
 * @ORM\Table(name="astillero_contratista_actividad_pausa")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Contratista\Actividad\PausaRepository")
 */
class Pausa
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
     * @ORM\Column(name="inicio", type="datetime")
     */
    private $inicio;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fin", type="datetime", nullable=true)
     */
    private $fin;

    /**
     * @var string
     *
     * @ORM\Column(name="nota", type="text")
     */
    private $nota;

    /**
     * @var string|null
     *
     * @ORM\Column(name="creador", type="string", length=255, nullable=true)
     */
    private $creador;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro", type="datetime")
     */
    private $registro;


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
     * Set inicio.
     *
     * @param \DateTime $inicio
     *
     * @return Pausa
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
     * @param \DateTime|null $fin
     *
     * @return Pausa
     */
    public function setFin($fin = null)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin.
     *
     * @return \DateTime|null
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set nota.
     *
     * @param string $nota
     *
     * @return Pausa
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota.
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set creador.
     *
     * @param string|null $creador
     *
     * @return Pausa
     */
    public function setCreador($creador = null)
    {
        $this->creador = $creador;

        return $this;
    }

    /**
     * Get creador.
     *
     * @return string|null
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * Set registro.
     *
     * @param \DateTime $registro
     *
     * @return Pausa
     */
    public function setRegistro($registro)
    {
        $this->registro = $registro;

        return $this;
    }

    /**
     * Get registro.
     *
     * @return \DateTime
     */
    public function getRegistro()
    {
        return $this->registro;
    }
}

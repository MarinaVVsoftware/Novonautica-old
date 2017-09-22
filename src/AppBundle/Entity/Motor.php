<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Motor
 *
 * @ORM\Table(name="motor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MotorRepository")
 */
class Motor
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
     * @ORM\Column(name="marca", type="string", length=100)
     */
    private $marca;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=100, nullable=true)
     */
    private $modelo;

    /**
     * @var string
     *
     * @ORM\Column(name="calado", type="string", length=100, nullable=true)
     */
    private $calado;

    /**
     * @var string
     *
     * @ORM\Column(name="manga", type="string", length=100, nullable=true)
     */
    private $manga;

    /**
     * @var string
     *
     * @ORM\Column(name="eslora", type="string", length=100, nullable=true)
     */
    private $eslora;

    /**
     * @var int
     *
     * @ORM\Column(name="combustible", type="integer", nullable=true)
     */
    private $combustible;

    /**
     * @var int
     *
     * @ORM\Column(name="agua", type="integer", nullable=true)
     */
    private $agua;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="motores")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id")
     */
    private $barco;

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
     * Set marca
     *
     * @param string $marca
     *
     * @return Motor
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     *
     * @return Motor
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return string
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set calado
     *
     * @param string $calado
     *
     * @return Motor
     */
    public function setCalado($calado)
    {
        $this->calado = $calado;

        return $this;
    }

    /**
     * Get calado
     *
     * @return string
     */
    public function getCalado()
    {
        return $this->calado;
    }

    /**
     * Set manga
     *
     * @param string $manga
     *
     * @return Motor
     */
    public function setManga($manga)
    {
        $this->modelo = $manga;

        return $this;
    }

    /**
     * Get manga
     *
     * @return string
     */
    public function getManga()
    {
        return $this->manga;
    }

    /**
     * Set eslora
     *
     * @param string $eslora
     *
     * @return Motor
     */
    public function setEslora($eslora)
    {
        $this->modelo = $eslora;

        return $this;
    }

    /**
     * Get eslora
     *
     * @return string
     */
    public function getEslora()
    {
        return $this->eslora;
    }

    /**
     * Set combustible
     *
     * @param integer $combustible
     *
     * @return Motor
     */
    public function setCombustible($combustible)
    {
        $this->combustible = $combustible;

        return $this;
    }

    /**
     * Get combustible
     *
     * @return int
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    /**
     * Set agua
     *
     * @param integer $agua
     *
     * @return Motor
     */
    public function setAgua($agua)
    {
        $this->combustible = $agua;

        return $this;
    }

    /**
     * Get agua
     *
     * @return int
     */
    public function getAgua()
    {
        return $this->agua;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return Motor
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set barco
     *
     * @param \AppBundle\Entity\Barco $barco
     *
     * @return Motor
     */
    public function setBarco(\AppBundle\Entity\Barco $barco = null)
    {
        $this->barco = $barco;
        return $this;
    }

    /**
     * Get barco
     *
     * @return \AppBundle\Entity\Barco
     */
    public function getBarco()
    {
        return $this->barco;
    }
}


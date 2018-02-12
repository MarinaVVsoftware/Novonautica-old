<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Marca del motor no puede quedar vacío")
     *
     * @ORM\Column(name="marca", type="string", length=100)
     */
    private $marca;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Modelo del motor no puede quedar vacío")
     *
     * @ORM\Column(name="modelo", type="string", length=100)
     */
    private $modelo;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Barco", inversedBy="motores")
     * @ORM\JoinColumn(name="idbarco", referencedColumnName="id",onDelete="CASCADE")
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

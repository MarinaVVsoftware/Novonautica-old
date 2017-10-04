<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ValorSistema
 *
 * @ORM\Table(name="valor_sistema")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ValorSistemaRepository")
 */
class ValorSistema
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
     * @var float
     * @Assert\NotBlank(
     *     message="El valor no puede quedar vacío"
     * )
     * @Assert\Type(
     *     type="float",
     *     message="El valor {{ value }} no es válido"
     * )
     *
     * @ORM\Column(name="valor", type="float")
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="unidad", type="string", length=255)
     */
    private $unidad;

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
     * @return ValorSistema
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
     * Set valor
     *
     * @param float $valor
     *
     * @return ValorSistema
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set unidad
     *
     * @param string $unidad
     *
     * @return ValorSistema
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
}

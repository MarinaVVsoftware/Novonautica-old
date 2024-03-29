<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MonederoMovimiento
 *
 * @ORM\Table(name="monedero_movimiento")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonederoMovimientoRepository")
 */
class MonederoMovimiento
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
     * @var integer
     * @Assert\NotBlank(
     *     message="El monto está en 0"
     * )
     *
     * @ORM\Column(name="monto", type="bigint", nullable=true)
     */
    private $monto;

    /**
     * @var string Operacion Matemarica: 1 suma, 2 resta
     *
     * @ORM\Column(name="operacion", type="string", length=4)
     */
    private $operacion;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Debes agregar una descripción de la operación"
     * )
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="resultante", type="bigint", nullable=true)
     */
    private $resultante;

    /**
     * @var int Tipo: 1 Marina Humeda, 2 Astillero, 3 Tienda
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;


    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="monederomovimientos")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

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
     * Set monto
     *
     * @param float $monto
     *
     * @return MonederoMovimiento
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set operacion
     *
     * @param string $operacion
     *
     * @return MonederoMovimiento
     */
    public function setOperacion($operacion)
    {
        $this->operacion = $operacion;

        return $this;
    }

    /**
     * Get operacion
     *
     * @return string
     */
    public function getOperacion()
    {
        return $this->operacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return MonederoMovimiento
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return MonederoMovimiento
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set resultante
     *
     * @param float $resultante
     *
     * @return MonederoMovimiento
     */
    public function setResultante($resultante)
    {
        $this->resultante = $resultante;

        return $this;
    }

    /**
     * Get resultante
     *
     * @return float
     */
    public function getResultante()
    {
        return $this->resultante;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     *
     * @return MonederoMovimiento
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return MonederoMovimiento
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}

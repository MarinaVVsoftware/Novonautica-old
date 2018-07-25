<?php

namespace AppBundle\Entity\Tienda\Inventario;

use AppBundle\Entity\Tienda\Inventario\Registro\Entrada;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registro
 *
 * @ORM\Table(name="tienda_inventario_registro")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\Inventario\RegistroRepository")
 */
class Registro
{
    CONST TIPO_SALIDA = 0;
    CONST TIPO_ENTRADA = 1;

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
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referencia", type="string", length=50, nullable=true)
     *w/
    private $referencia;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="bigint")
     */
    private $total;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tipo", type="boolean")
     */
    private $tipo;

    public static $tipoList = [
        self::TIPO_SALIDA => 'Salida',
        self::TIPO_ENTRADA => 'Entrada',
    ];

    /**
     * @var Entrada[]
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Tienda\Inventario\Registro\Entrada",
     *     mappedBy="registro",
     *     cascade={"persist"}
     * )
     */
    private $entradas;

    public function __construct()
    {
        $this->entradas = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param string|null $referencia
     */
    public function setReferencia($referencia = null)
    {
        $this->referencia = $referencia;
    }

    /**
     * @return string|null
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param bool $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getTipoNamed()
    {
        if (null === $this->tipo) {
            return null;
        }

        return self::$tipoList[$this->tipo];
    }

    /**
     * @param Entrada $entrada
     */
    public function addEntrada(Entrada $entrada)
    {
        $entrada->setRegistro($this);
        $this->entradas[] = $entrada;
    }

    /**
     * @param Entrada $entrada
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEntrada(Entrada $entrada)
    {
        return $this->entradas->removeElement($entrada);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntradas()
    {
        return $this->entradas;
    }
}

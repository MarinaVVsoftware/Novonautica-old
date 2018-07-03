<?php

namespace AppBundle\Entity\Tienda\Inventario\Registro;

use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Producto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entrada
 *
 * @ORM\Table(name="tienda_inventario_registro_entrada")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Tienda\Inventario\Registro\EntradaRepository")
 */
class Entrada
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
     * @var int
     *
     * @Assert\Range(
     *     min="1",
     *     minMessage="La cantidad minima es 1"
     * )
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="importe", type="integer")
     */
    private $importe;

    /**
     * @var Producto
     *
     * @Assert\NotNull(message="Esta campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tienda\Producto")
     */
    private $producto;

    /**
     * @var Registro
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Tienda\Inventario\Registro",
     *     inversedBy="entradas",
     *     cascade={"persist"}
     * )
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
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param int $importe
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    /**
     * @return int
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * @return Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * @param Producto $producto
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;
    }

    /**
     * @param Registro|null $registro
     */
    public function setRegistro(Registro $registro = null)
    {
        $this->registro = $registro;
    }

    /**
     * @return Registro|null
     */
    public function getRegistro()
    {
        return $this->registro;
    }
}

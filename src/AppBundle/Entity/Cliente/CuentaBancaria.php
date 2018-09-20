<?php

namespace AppBundle\Entity\Cliente;

use AppBundle\Entity\Cliente;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CuentaBancaria
 *
 * @ORM\Table(name="cliente_cuenta_bancaria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cliente\CuentaBancariaRepository")
 */
class CuentaBancaria
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
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="alias", type="string", length=50)
     */
    private $alias;

    /**
     * @var string
     *
     * @Assert\Regex(
     *     pattern="/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/",
     *     message="El RFC es invalido"
     *     )
     *
     * @ORM\Column(name="rfc", type="string", length=20)
     */
    private $rfc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="numero_cuenta", type="string", length=25)
     */
    private $numeroCuenta;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="cuentasBancarias")
     */
    private $cliente;

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
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Set rfc.
     *
     * @param string $rfc
     *
     * @return CuentaBancaria
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;

        return $this;
    }

    /**
     * Get rfc.
     *
     * @return string
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return CuentaBancaria
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
     * Set numeroCuenta.
     *
     * @param string $numeroCuenta
     *
     * @return CuentaBancaria
     */
    public function setNumeroCuenta($numeroCuenta)
    {
        $this->numeroCuenta = $numeroCuenta;

        return $this;
    }

    /**
     * Get numeroCuenta.
     *
     * @return string
     */
    public function getNumeroCuenta()
    {
        return $this->numeroCuenta;
    }

    /**
     * Set cliente.
     *
     * @param Cliente|null $cliente
     *
     * @return CuentaBancaria
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente.
     *
     * @return Cliente|null
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}

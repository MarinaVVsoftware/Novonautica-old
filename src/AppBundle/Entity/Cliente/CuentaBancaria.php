<?php

namespace AppBundle\Entity\Cliente;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Contabilidad\Banco;
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
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="numero_cuenta", type="string", length=25)
     */
    private $numeroCuenta;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="clabe", type="string", length=25)
     */
    private $clabe;

    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="cuentasBancarias")
     */
    private $cliente;

    /**
     * @var Banco
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Banco")
     */
    private $banco;

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
     * @return string
     */
    public function getClabe()
    {
        return $this->clabe;
    }

    /**
     * @param string $clabe
     */
    public function setClabe($clabe)
    {
        $this->clabe = $clabe;
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

    /**
     * @return Banco
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * @param Banco $banco
     */
    public function setBanco(Banco $banco = null)
    {
        $this->banco = $banco;
    }
}

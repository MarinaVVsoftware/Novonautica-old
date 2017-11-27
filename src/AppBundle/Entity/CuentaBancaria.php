<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaBancaria
 *
 * @ORM\Table(name="cuenta_bancaria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CuentaBancariaRepository")
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
     * @ORM\Column(name="banco", type="string", length=80)
     */
    private $banco;

    /**
     * @var string
     *
     * @ORM\Column(name="clabe", type="string", length=100)
     */
    private $clabe;


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
     * Set banco
     *
     * @param string $banco
     *
     * @return CuentaBancaria
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return string
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set clabe
     *
     * @param string $clabe
     *
     * @return CuentaBancaria
     */
    public function setClabe($clabe)
    {
        $this->clabe = $clabe;

        return $this;
    }

    /**
     * Get clabe
     *
     * @return string
     */
    public function getClabe()
    {
        return $this->clabe;
    }
}


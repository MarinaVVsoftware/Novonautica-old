<?php

namespace AppBundle\Entity\Cliente;

use AppBundle\Entity\Cliente;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RazonSocial
 *
 * @ORM\Table(name="cliente_razon_social")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cliente\RazonSocialRepository")
 */
class RazonSocial
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
     * @Assert\Regex(
     *     pattern="/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/",
     *     message="El RFC es invalido"
     *     )
     *
     * @Groups({"group1"})
     *
     * @ORM\Column(name="rfc", type="string", length=50)
     */
    private $rfc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @Groups({"group1"})
     *
     * @ORM\Column(name="razon_social", type="string", length=255)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="uso_cfdi", type="string", length=10)
     */
    private $usoCFDI;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @Groups({"group1"})
     *
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * @var array
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @Groups({"group1"})
     *
     * @ORM\Column(name="correos", type="string", nullable=true)
     */
    private $correos;

    /**
     * @var Cliente
     *
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="razonesSociales")
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
     * Set rfc
     *
     * @param string $rfc
     *
     * @return RazonSocial
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;

        return $this;
    }

    /**
     * Get rfc
     *
     * @return string
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return RazonSocial
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * @return string
     */
    public function getUsoCFDI()
    {
        return $this->usoCFDI;
    }

    /**
     * @param string $usoCFDI
     */
    public function setUsoCFDI($usoCFDI)
    {
        $this->usoCFDI = $usoCFDI;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return RazonSocial
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }


    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set correos
     *
     * @param array $correos
     *
     * @return RazonSocial
     */
    public function setCorreos($correos)
    {
        $this->correos = $correos;

        return $this;
    }

    /**
     * Get correos
     *
     * @return array
     */
    public function getCorreos()
    {
        return $this->correos;
    }

    /**
     * Set cliente
     *
     * @param Cliente $cliente
     *
     * @return RazonSocial
     */
    public function setCliente(Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}

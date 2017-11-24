<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pago
 *
 * @ORM\Table(name="pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PagoRepository")
 */
class Pago
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
     * @ORM\Column(name="metodopago", type="string", length=100)
     */
    private $metodopago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechapago", type="datetime", nullable=true)
     */
    private $fechapago;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", inversedBy="pago")
     * @ORM\JoinColumn(name="idmhcotizacion", referencedColumnName="id")
     */
    private $mhcotizacion;

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
     * Set metodopago
     *
     * @param string $metodopago
     *
     * @return Pago
     */
    public function setMetodopago($metodopago)
    {
        $this->metodopago = $metodopago;

        return $this;
    }

    /**
     * Get metodopago
     *
     * @return string
     */
    public function getMetodopago()
    {
        return $this->metodopago;
    }

    /**
     * Set fechapago
     *
     * @param \DateTime $fechapago
     *
     * @return Pago
     */
    public function setFechapago($fechapago)
    {
        $this->fechapago = $fechapago;

        return $this;
    }

    /**
     * Get fechapago
     *
     * @return \DateTime
     */
    public function getFechapago()
    {
        return $this->fechapago;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Pago
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set fecharegistro
     *
     * @param \DateTime $fecharegistro
     *
     * @return Pago
     */
    public function setFecharegistro($fecharegistro)
    {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    /**
     * Get fecharegistro
     *
     * @return \DateTime
     */
    public function getFecharegistro()
    {
        return $this->fecharegistro;
    }

    /**
     * Set mhcotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacion
     *
     * @return Pago
     */
    public function setMhcotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacion = null)
    {
        $this->mhcotizacion = $mhcotizacion;

        return $this;
    }

    /**
     * Get mhcotizacion
     *
     * @return \AppBundle\Entity\MarinaHumedaCotizacion
     */
    public function getMhcotizacion()
    {
        return $this->mhcotizacion;
    }
}

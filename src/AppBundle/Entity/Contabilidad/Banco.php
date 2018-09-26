<?php

namespace AppBundle\Entity\Contabilidad;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banco
 *
 * @ORM\Table(name="contabilidad_banco")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\BancoRepository")
 */
class Banco
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
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="clave", type="integer")
     */
    private $clave;

    /**
     * @var string
     *
     * @Assert\Regex(
     *     pattern="/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/",
     *     message="El RFC es invalido"
     *     )
     *
     * @ORM\Column(name="rfc", type="string", length=50)
     */
    private $RFC;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="razon_social", type="string", length=255)
     */
    private $razonSocial;

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
     * Set clave.
     *
     * @param int $clave
     *
     * @return Banco
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get clave.
     *
     * @return int
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set rFC.
     *
     * @param string $rFC
     *
     * @return Banco
     */
    public function setRFC($rFC)
    {
        $this->RFC = $rFC;

        return $this;
    }

    /**
     * Get rFC.
     *
     * @return string
     */
    public function getRFC()
    {
        return $this->RFC;
    }

    /**
     * Set razonSocial.
     *
     * @param string $razonSocial
     *
     * @return Banco
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial.
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }
}

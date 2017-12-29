<?php

namespace AppBundle\Entity\Contabilidad\Facturacion;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emisor
 *
 * @ORM\Table(name="contabilidad_facturacion_emisor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Facturacion\EmisorRepository")
 */
class Emisor
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
     * @ORM\Column(name="rfc", type="string", length=20)
     */
    private $rfc;

    /**
     * @var string
     *
     * @ORM\Column(name="regimen_fiscal", type="string", length=10)
     */
    private $regimenFiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=10)
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string")
     */
    private $direccion;

    public function __toString()
    {
        return $this->nombre;
    }

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
     * @return Emisor
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
     * @return string
     */
    public function getRegimenFiscal()
    {
        return $this->regimenFiscal;
    }

    /**
     * @param string $regimenFiscal
     */
    public function setRegimenFiscal($regimenFiscal)
    {
        $this->regimenFiscal = $regimenFiscal;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Emisor
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
     * Set codigoPostal
     *
     * @param string $codigoPostal
     *
     * @return Emisor
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Emisor
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
}

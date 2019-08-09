<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;

/**
 * AstilleroServicio
 *
 * @ORM\Table(name="astillero_servicio_basico")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AstilleroServicioBasicoRepository")
 */
class AstilleroServicioBasico
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
     * @Groups({"facturacion"})
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var decimal
     *
     * @ORM\Column(name="precio", type="decimal", precision=19, scale=4)
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=3)
     */
    private $divisa;

    /**
     * @var ClaveUnidad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;
    /**
     * @var ClaveProdServ
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return AstilleroServicioBasico
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
     * Set precio.
     *
     * @param decimal|null $precio
     *
     * @return AstilleroServicioBasico
     */
    public function setPrecio($precio = null)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return decimal|null
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set divisa.
     *
     * @param string $divisa
     *
     * @return AstilleroServicioBasico
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     *
     * @return AstilleroServicioBasico
     */
    public function setClaveUnidad(ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;
        return $this;
    }

    /**
     * Get claveUnidad.
     *
     * @return ClaveUnidad|null
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set claveProdServ.
     *
     * @param ClaveProdServ|null $claveProdServ
     *
     * @return AstilleroServicioBasico
     */
    public function setClaveProdServ(ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;
        return $this;
    }

    /**
     * Get claveProdServ.
     *
     * @return ClaveProdServ|null
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

}

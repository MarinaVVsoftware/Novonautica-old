<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * MarinaHumedaServicio
 *
 * @ORM\Table(name="marina_humeda_servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaServicioRepository")
 */
class MarinaHumedaServicio
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
     * @Assert\NotBlank(
     *     message="Nombre no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message="Unidad no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="unidad", type="string", length=10)
     */
    private $unidad;

    /**
     * @var integer
     * @Assert\NotBlank(
     *     message="Precio no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="precio", type="integer", nullable=true)
     */
    private $precio;

    /**
     * @var float
     *
     * @ORM\Column(name="existencia", type="float", nullable=true)
     */
    private $existencia;

    /**
     * @var ClaveProdServ
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

    /**
     * @var ClaveUnidad
     *
     * @Groups({"facturacion"})
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return MarinaHumedaServicio
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
     * Set unidad
     *
     * @param string $unidad
     *
     * @return MarinaHumedaServicio
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param int $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }



    /**
     * Set existencia.
     *
     * @param float|null $existencia
     *
     * @return MarinaHumedaServicio
     */
    public function setExistencia($existencia = null)
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia.
     *
     * @return float|null
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Set claveProdServ.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ|null $claveProdServ
     *
     * @return MarinaHumedaServicio
     */
    public function setClaveProdServ(\AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;

        return $this;
    }

    /**
     * Get claveProdServ.
     *
     * @return \AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ|null
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set claveUnidad.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad|null $claveUnidad
     *
     * @return MarinaHumedaServicio
     */
    public function setClaveUnidad(\AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;

        return $this;
    }

    /**
     * Get claveUnidad.
     *
     * @return \AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad|null
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }
}

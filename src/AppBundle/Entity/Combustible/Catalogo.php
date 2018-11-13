<?php

namespace AppBundle\Entity\Combustible;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\Mapping as ORM;

/**
 * Catalogo
 *
 * @ORM\Table(name="combustible_catalogo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Combustible\CatalogoRepository")
 */
class Catalogo
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
     * @ORM\Column(name="nombre", type="string", length=20)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var float|null
     *
     * @ORM\Column(name="cuota_iesps", type="float", nullable=true)
     */
    private $cuotaIesps;

    /**
     * @var float
     *
     * @ORM\Column(name="existencia", type="float", nullable=true)
     */
    private $existencia;

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

    public function __toString()
    {
      return $this->nombre;
    }

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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Catalogo
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
     * Set precio.
     *
     * @param int $precio
     *
     * @return Catalogo
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio.
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set cuotaIesps.
     *
     * @param float|null $cuotaIesps
     *
     * @return Catalogo
     */
    public function setCuotaIesps($cuotaIesps = null)
    {
        $this->cuotaIesps = $cuotaIesps;

        return $this;
    }

    /**
     * Get cuotaIesps.
     *
     * @return float|null
     */
    public function getCuotaIesps()
    {
        return $this->cuotaIesps;
    }

    /**
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     *
     * @return Catalogo
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
     * @return Catalogo
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

    /**
     * Set existencia.
     *
     * @param float|null $existencia
     *
     * @return Catalogo
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
}

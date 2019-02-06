<?php

namespace AppBundle\Entity\Marina\Tarifa;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tipo
 *
 * @ORM\Table(name="marina_humeda_cotizacion_tarifa_tipo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Marina\Tarifa\TipoRepository")
 */
class Tipo
{
    const TIPO_AMARRE = 1;
    const TIPO_ELECTRICIDAD = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;

    /**
     * @var ClaveProdServ
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

    /**
     * @var ClaveUnidad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

    public static $tipos = [
      'Amarre' => self::TIPO_AMARRE,
      'Electricidad' => self::TIPO_ELECTRICIDAD,
    ];

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
     * Set tipo.
     *
     * @param int $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get tipo.
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Get name of the tipo
     *
     * @return string
     */
    public function getNamedTipo()
    {
        return self::$tipos[$this->tipo];
    }

    /**
     * Set claveProdServ.
     *
     * @param ClaveProdServ|null $claveProdServ
     */
    public function setClaveProdServ(ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;
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
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     */
    public function setClaveUnidad(ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;
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
}

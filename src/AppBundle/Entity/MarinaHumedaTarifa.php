<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarinaHumedaTarifa
 *
 * @ORM\Table(name="marina_humeda_tarifa")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarinaHumedaTarifaRepository")
 */
class MarinaHumedaTarifa
{
    const CLASIFICACION_GENERAL = 0;
    const CLASIFICACION_ESPECIAL = 1;

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
     * @var int
     *
     * @ORM\Column(name="costo", type="integer")
     */
    private $costo;

    /**
     * @var float
     *
     * @ORM\Column(name="pies_a", type="float", nullable=true)
     */
    private $piesA;

    /**
     * @var float
     *
     * @ORM\Column(name="pies_b", type="float", nullable=true)
     */
    private $piesB;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="clasificacion", type="smallint")
     */
    private $clasificacion;

    private static $clasificacionList = [
        MarinaHumedaTarifa::CLASIFICACION_GENERAL => 'Tarifa General',
        MarinaHumedaTarifa::CLASIFICACION_ESPECIAL => 'Tarifa Especial'
    ];


    public function __toString()
    {
        return '$' . ($this->costo / 100) . ' USD - Entre '.$this->piesA.' fts y '.$this->piesB.' fts - (' . $this->descripcion . ')';
    }


    public function __construct()
    {
        $this->clasificacion = 0;
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
     * Set tipo
     *
     * @param integer $tipo
     *
     * @return MarinaHumedaTarifa
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set costo
     *
     * @param int $costo
     *
     * @return MarinaHumedaTarifa
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;

        return $this;
    }

    /**
     * Get costo
     *
     * @return int
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return MarinaHumedaTarifa
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set piesA.
     *
     * @param float|null $piesA
     *
     * @return MarinaHumedaTarifa
     */
    public function setPiesA($piesA = null)
    {
        $this->piesA = $piesA;

        return $this;
    }

    /**
     * Get piesA.
     *
     * @return float|null
     */
    public function getPiesA()
    {
        return $this->piesA;
    }

    /**
     * Set piesB.
     *
     * @param float|null $piesB
     *
     * @return MarinaHumedaTarifa
     */
    public function setPiesB($piesB = null)
    {
        $this->piesB = $piesB;

        return $this;
    }

    /**
     * Get piesB.
     *
     * @return float|null
     */
    public function getPiesB()
    {
        return $this->piesB;
    }

    /**
     * @return int
     */
    public function getClasificacion()
    {
        if (null === $this->clasificacion) { return null; }
        return $this->clasificacion;
    }

    /**
     * @return int
     */
    public function getClasificacionNombre()
    {
        if (null === $this->clasificacion) { return null; }
        return self::$clasificacionList[$this->clasificacion];
    }

    /**
     * @param int $clasificacion
     */
    public function setClasificacion($clasificacion)
    {
        $this->clasificacion = $clasificacion;
    }

    public static function getClasificacionList()
    {
        return self::$clasificacionList;
    }
}

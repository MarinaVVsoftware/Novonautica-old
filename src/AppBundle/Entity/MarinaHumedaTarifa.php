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

    const CONDICION_INDEFINIDO = 0;
    const CONDICION_MENOR_IGUAL = 1;
    const CONDICION_MENOR = 2;
    const CONDICION_MAYOR_IGUAL = 3;
    const CONDICION_MAYOR = 4;
    const CONDICION_ENTRE = 5;

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
     * @ORM\Column(name="condicion", type="smallint")
     */
    private $condicion;

    private static $condicionList = [
      MarinaHumedaTarifa::CONDICION_INDEFINIDO => 'Indefinido',
      MarinaHumedaTarifa::CONDICION_MENOR_IGUAL => 'Menor o igual que',
      MarinaHumedaTarifa::CONDICION_MENOR => 'Menor que',
      MarinaHumedaTarifa::CONDICION_MAYOR_IGUAL => 'Mayor o igual que',
      MarinaHumedaTarifa::CONDICION_MAYOR => 'Mayor que',
      MarinaHumedaTarifa::CONDICION_ENTRE => 'Entre'
    ];

    public function __toString()
    {
        return '$' . ($this->costo / 100) . ' USD - ' . $this->getCondicionCompleta() . ' - (' . $this->descripcion . ')';
    }

    public function getCondicionCompleta()
    {
        return $this->getCondicionNombre() . ' ' . ($this->condicion === 0
                ? ''
                : $this->piesA . ' fts ' . ($this->condicion === 5
                    ? ' y ' . $this->piesB . ' fts'
                    : ''));
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
     * @return int
     */
    public function getCondicion()
    {
        if (null === $this->condicion) { return null; }
        return $this->condicion;
    }

    /**
     * @return int
     */
    public function getCondicionNombre()
    {
        if (null === $this->condicion) { return null; }
        return self::$condicionList[$this->condicion];
    }

    /**
     * @param int $condicion
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;
    }

    public static function getCondicionList()
    {
        return self::$condicionList;
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
}

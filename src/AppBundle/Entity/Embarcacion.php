<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Embarcacion
 *
 * @ORM\Table(name="embarcacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionRepository")
 */
class Embarcacion
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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="integer")
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="construccion", type="string", length=100)
     */
    private $construccion;

    /**
     * @var string
     *
     * @ORM\Column(name="ano", type="string", length=20)
     */
    private $ano;

    /**
     * @var float
     *
     * @ORM\Column(name="longitud", type="float")
     */
    private $longitud;

    /**
     * @var float
     *
     * @ORM\Column(name="eslora", type="float")
     */
    private $eslora;

    /**
     * @var float
     *
     * @ORM\Column(name="manga", type="float")
     */
    private $manga;

    /**
     * @var float
     *
     * @ORM\Column(name="calado", type="float")
     */
    private $calado;

    /**
     * @var float
     *
     * @ORM\Column(name="peso", type="float")
     */
    private $peso;

    /**
     * @var float
     *
     * @ORM\Column(name="capacidad_combustible", type="float")
     */
    private $capacidadCombustible;

    /**
     * @var float
     *
     * @ORM\Column(name="capacidad_agua", type="float")
     */
    private $capacidadAgua;

    /**
     * Este valor es donde van los residuos del barco
     *
     * @var float
     *
     * @ORM\Column(name="capacidad_deposito", type="float")
     */
    private $capacidadDeposito;

    /**
     * @var int
     *
     * @ORM\Column(name="cabinas", type="integer")
     */
    private $cabinas;

    /**
     * @var int
     *
     * @ORM\Column(name="pasajeros_dormidos", type="integer")
     */
    private $pasajerosDormidos;

    /**
     * Generador de electricidad
     *
     * @var string
     *
     * @ORM\Column(name="generador", type="string", length=100)
     */
    private $generador;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var EmbarcacionMarca
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmbarcacionMarca")
     */
    private $marca;

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
     * @return Embarcacion
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
     * Set precio
     *
     * @param integer $precio
     *
     * @return Embarcacion
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @return string
     */
    public function getConstruccion()
    {
        return $this->construccion;
    }

    /**
     * @param string $construccion
     */
    public function setConstruccion($construccion)
    {
        $this->construccion = $construccion;
    }

    /**
     * Set ano
     *
     * @param string $ano
     *
     * @return Embarcacion
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get ano
     *
     * @return string
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set longitud
     *
     * @param float $longitud
     *
     * @return Embarcacion
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return float
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set eslora
     *
     * @param float $eslora
     *
     * @return Embarcacion
     */
    public function setEslora($eslora)
    {
        $this->eslora = $eslora;

        return $this;
    }

    /**
     * Get eslora
     *
     * @return float
     */
    public function getEslora()
    {
        return $this->eslora;
    }

    /**
     * Set manga
     *
     * @param float $manga
     *
     * @return Embarcacion
     */
    public function setManga($manga)
    {
        $this->manga = $manga;

        return $this;
    }

    /**
     * Get manga
     *
     * @return float
     */
    public function getManga()
    {
        return $this->manga;
    }

    /**
     * Set calado
     *
     * @param float $calado
     *
     * @return Embarcacion
     */
    public function setCalado($calado)
    {
        $this->calado = $calado;

        return $this;
    }

    /**
     * Get calado
     *
     * @return float
     */
    public function getCalado()
    {
        return $this->calado;
    }

    /**
     * Set peso
     *
     * @param float $peso
     *
     * @return Embarcacion
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return float
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set capacidadCombustible
     *
     * @param float $capacidadCombustible
     *
     * @return Embarcacion
     */
    public function setCapacidadCombustible($capacidadCombustible)
    {
        $this->capacidadCombustible = $capacidadCombustible;

        return $this;
    }

    /**
     * Get capacidadCombustible
     *
     * @return float
     */
    public function getCapacidadCombustible()
    {
        return $this->capacidadCombustible;
    }

    /**
     * Set capacidadAgua
     *
     * @param float $capacidadAgua
     *
     * @return Embarcacion
     */
    public function setCapacidadAgua($capacidadAgua)
    {
        $this->capacidadAgua = $capacidadAgua;

        return $this;
    }

    /**
     * Get capacidadAgua
     *
     * @return float
     */
    public function getCapacidadAgua()
    {
        return $this->capacidadAgua;
    }

    /**
     * Set capacidadDeposito
     *
     * @param float $capacidadDeposito
     *
     * @return Embarcacion
     */
    public function setCapacidadDeposito($capacidadDeposito)
    {
        $this->capacidadDeposito = $capacidadDeposito;

        return $this;
    }

    /**
     * Get capacidadDeposito
     *
     * @return float
     */
    public function getCapacidadDeposito()
    {
        return $this->capacidadDeposito;
    }

    /**
     * Set cabinas
     *
     * @param integer $cabinas
     *
     * @return Embarcacion
     */
    public function setCabinas($cabinas)
    {
        $this->cabinas = $cabinas;

        return $this;
    }

    /**
     * Get cabinas
     *
     * @return integer
     */
    public function getCabinas()
    {
        return $this->cabinas;
    }

    /**
     * Set pasajerosDormidos
     *
     * @param integer $pasajerosDormidos
     *
     * @return Embarcacion
     */
    public function setPasajerosDormidos($pasajerosDormidos)
    {
        $this->pasajerosDormidos = $pasajerosDormidos;

        return $this;
    }

    /**
     * Get pasajerosDormidos
     *
     * @return integer
     */
    public function getPasajerosDormidos()
    {
        return $this->pasajerosDormidos;
    }

    /**
     * Set generador
     *
     * @param string $generador
     *
     * @return Embarcacion
     */
    public function setGenerador($generador)
    {
        $this->generador = $generador;

        return $this;
    }

    /**
     * Get generador
     *
     * @return string
     */
    public function getGenerador()
    {
        return $this->generador;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Embarcacion
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
     * @return EmbarcacionMarca
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * @param EmbarcacionMarca $marca
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;
    }
}

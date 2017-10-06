<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Barco
 *
 * @ORM\Table(name="barco")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BarcoRepository")
 */
class Barco
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="marca", type="string", length=100, nullable=true)
     */
    private $marca;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=100, nullable=true)
     */
    private $modelo;

    /**
     * @var int
     *
     * @ORM\Column(name="anio", type="integer", nullable=true)
     */
    private $anio;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente", inversedBy="barcos")
     * @ORM\JoinColumn(name="idcliente", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cliente;

    /**
     * @ORM\OneToMany(targetEntity="Motor", mappedBy="barco",cascade={"persist"})
     */
    private $motores;

    /**
     * @ORM\OneToMany(targetEntity="MarinaHumedaCotizacion", mappedBy="barco")
     */
    private $mhcotizaciones;

    public function __construct() {
        $this->motores = new ArrayCollection();
        $this->mhcotizaciones = new ArrayCollection();
    }

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
     * @return Barco
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
     * Set marca
     *
     * @param string $marca
     *
     * @return Barco
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     *
     * @return Barco
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return string
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set anio
     *
     * @param integer $anio
     *
     * @return Barco
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return int
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return Barco
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return bool
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return Barco
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;
        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Get motores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotores()
    {
        return $this->motores;
    }

    /**
     * Add motore
     *
     * @param \AppBundle\Entity\Motor $motore
     *
     * @return Barco
     */
    public function addMotore(\AppBundle\Entity\Motor $motore)
    {
        $motore->setBarco($this);
//        $this->motores->add($motore);
        $this->motores[] = $motore;

        return $this;
    }

    /**
     * Remove motore
     *
     * @param \AppBundle\Entity\Motor $motore
     */
    public function removeMotore(\AppBundle\Entity\Motor $motore)
    {
        $this->motores->removeElement($motore);
    }



    /**
     * Add marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return Barco
     */
    public function addMarinaHumedaCotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion)
    {
        $marinahumedacotizacion->setBarco($this);
        $this->mhcotizaciones[] = $marinahumedacotizacion;
        return $this;
    }

    /**
     * Remove marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     */
    public function removeMarinaHumedaCotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion)
    {
        $this->mhcotizaciones->removeElement($marinahumedacotizacion);
    }

    /**
     * Get mhcotizaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMHcotizaciones()
    {
        return $this->mhcotizaciones;
    }

    /**
     * Add mhcotizacione
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione
     *
     * @return Barco
     */
    public function addMhcotizacione(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones[] = $mhcotizacione;

        return $this;
    }

    /**
     * Remove mhcotizacione
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione
     */
    public function removeMhcotizacione(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones->removeElement($mhcotizacione);
    }
}

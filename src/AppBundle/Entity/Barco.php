<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Groups({"currentOcupation"})
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Groups({"currentOcupation"})
     *
     * @Assert\NotBlank(message="Nombre del barco no puede quedar vacío")
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=100)
     */
    private $modelo;

    /**
     * @var string
     *
     * @ORM\Column(name="calado", type="string", length=100, nullable=true)
     */
    private $calado;

    /**
     * @var string
     *
     * @ORM\Column(name="manga", type="string", length=100, nullable=true)
     */
    private $manga;

    /**
     * @var string
     *
     * @ORM\Column(name="eslora", type="string", length=100)
     */
    private $eslora;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_responsable", type="string", nullable=true)
     */
    private $nombreResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono_responsable", type="string", nullable=true)
     */
    private $telefonoResponsable;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "El correo '{{ value }}' no es válido."
     * )
     *
     * @ORM\Column(name="correo_responsable", type="string", length=255, nullable=true)
     */
    private $correoResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_capitan", type="string", length=255, nullable=true)
     */
    private $nombreCapitan;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono_capitan", type="string", length=255, nullable=true)
     */
    private $telefonoCapitan;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "El correo '{{ value }}' no es válido."
     * )
     *
     * @ORM\Column(name="correo_capitan", type="string", length=255, nullable=true)
     */
    private $correoCapitan;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

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
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="Motor", mappedBy="barco", cascade={"persist"})
     */
    private $motores;

    /**
     * @ORM\OneToMany(targetEntity="MarinaHumedaCotizacion", mappedBy="barco")
     */
    private $mhcotizaciones;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacionAdicional", mappedBy="barco")
     */
    private $mhcotizacionesadicionales;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizacion", mappedBy="barco")
     */
    private $astillerocotizaciones;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tienda\Solicitud", mappedBy="nombrebarco")
     */
    private $embarcacion;

    public function __construct()
    {
        $this->motores = new ArrayCollection();
        $this->mhcotizaciones = new ArrayCollection();
        $this->mhcotizacionesadicionales = new ArrayCollection();
        $this->astillerocotizaciones = new ArrayCollection();
        $this->embarcacion = new ArrayCollection();
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
     * Set calado
     *
     * @param string $calado
     *
     * @return Barco
     */
    public function setCalado($calado)
    {
        $this->calado = $calado;

        return $this;
    }

    /**
     * Get calado
     *
     * @return string
     */
    public function getCalado()
    {
        return $this->calado;
    }

    /**
     * Set manga
     *
     * @param string $manga
     *
     * @return Barco
     */
    public function setManga($manga)
    {
        $this->manga = $manga;

        return $this;
    }

    /**
     * Get manga
     *
     * @return string
     */
    public function getManga()
    {
        return $this->manga;
    }

    /**
     * Set eslora
     *
     * @param string $eslora
     *
     * @return Barco
     */
    public function setEslora($eslora)
    {
        $this->eslora = $eslora;

        return $this;
    }

    /**
     * Get eslora
     *
     * @return string
     */
    public function getEslora()
    {
        return $this->eslora;
    }

    /**
     * @return string
     */
    public function getNombreResponsable()
    {
        return $this->nombreResponsable;
    }

    /**
     * @param string $nombreResponsable
     */
    public function setNombreResponsable($nombreResponsable)
    {
        $this->nombreResponsable = $nombreResponsable;
    }

    /**
     * @return string
     */
    public function getTelefonoResponsable()
    {
        return $this->telefonoResponsable;
    }

    /**
     * @param string $telefonoResponsable
     */
    public function setTelefonoResponsable($telefonoResponsable)
    {
        $this->telefonoResponsable = $telefonoResponsable;
    }

    /**
     * @return string
     */
    public function getCorreoResponsable()
    {
        return $this->correoResponsable;
    }

    /**
     * @param string $correoResponsable
     */
    public function setCorreoResponsable($correoResponsable)
    {
        $this->correoResponsable = $correoResponsable;
    }

    /**
     * Set nombreCapitan
     *
     * @param string $nombreCapitan
     *
     * @return Barco
     */
    public function setNombreCapitan($nombreCapitan)
    {
        $this->nombreCapitan = $nombreCapitan;

        return $this;
    }

    /**
     * Get nombreCapitan
     *
     * @return string
     */
    public function getNombreCapitan()
    {
        return $this->nombreCapitan;
    }

    /**
     * Set telefonoCapitan
     *
     * @param string $telefonoCapitan
     *
     * @return Barco
     */
    public function setTelefonoCapitan($telefonoCapitan)
    {
        $this->telefonoCapitan = $telefonoCapitan;

        return $this;
    }

    /**
     * Get telefonoCapitan
     *
     * @return string
     */
    public function getTelefonoCapitan()
    {
        return $this->telefonoCapitan;
    }

    /**
     * Set correoCapitan
     *
     * @param string $correoCapitan
     *
     * @return Barco
     */
    public function setCorreoCapitan($correoCapitan)
    {
        $this->correoCapitan = $correoCapitan;

        return $this;
    }

    /**
     * Get correoCapitan
     *
     * @return string
     */
    public function getCorreoCapitan()
    {
        return $this->correoCapitan;
    }

    /**
     * Set fecharegistro
     *
     * @param \DateTime $fecharegistro
     *
     * @return Barco
     */
    public function setFecharegistro($fecharegistro)
    {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    /**
     * Get fecharegistro
     *
     * @return \DateTime
     */
    public function getFecharegistro()
    {
        return $this->fecharegistro;
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


    /**
     * Add astillerocotizacione
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astillerocotizacione
     *
     * @return Barco
     */
    public function addAstillerocotizacione(\AppBundle\Entity\AstilleroCotizacion $astillerocotizacione)
    {
        $this->astillerocotizaciones[] = $astillerocotizacione;

        return $this;
    }

    /**
     * Remove astillerocotizacione
     *
     * @param \AppBundle\Entity\AstilleroCotizacion $astillerocotizacione
     */
    public function removeAstillerocotizacione(\AppBundle\Entity\AstilleroCotizacion $astillerocotizacione)
    {
        $this->astillerocotizaciones->removeElement($astillerocotizacione);
    }

    /**
     * Get astillerocotizaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAstillerocotizaciones()
    {
        return $this->astillerocotizaciones;
    }


    /**
     * Add mhcotizacionesadicionale
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale
     *
     * @return Barco
     */
    public function addMhcotizacionesadicionale(\AppBundle\Entity\MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale)
    {
        $this->mhcotizacionesadicionales[] = $mhcotizacionesadicionale;

        return $this;
    }

    /**
     * Remove mhcotizacionesadicionale
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale
     */
    public function removeMhcotizacionesadicionale(\AppBundle\Entity\MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale)
    {
        $this->mhcotizacionesadicionales->removeElement($mhcotizacionesadicionale);
    }

    /**
     * Get mhcotizacionesadicionales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMhcotizacionesadicionales()
    {
        return $this->mhcotizacionesadicionales;
    }

    /**
     * Add embarcacion
     *
     * @param \AppBundle\Entity\Tienda\Solicitud $embarcacion
     *
     * @return Barco
     */
    public function addEmbarcacion(\AppBundle\Entity\Tienda\Solicitud $embarcacion)
    {
        $this->embarcacion[] = $embarcacion;

        return $this;
    }

    /**
     * Remove embarcacion
     *
     * @param \AppBundle\Entity\Tienda\Solicitud $embarcacion
     */
    public function removeEmbarcacion(\AppBundle\Entity\Tienda\Solicitud $embarcacion)
    {
        $this->embarcacion->removeElement($embarcacion);
    }

    /**
     * Get embarcacion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }
}

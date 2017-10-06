<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClienteRepository")
 */
class Cliente
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @var string
     * @Assert\Email(
     *     message = "El correo '{{ value }}' no es válido."
     * )
     *
     * @ORM\Column(name="correo", type="string", length=255, unique=true)
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horaregistro", type="time", nullable=true)
     */
    private $horaregistro;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string", length=255, nullable=true)
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="razonsocial", type="string", length=255, nullable=true)
     */
    private $razonsocial;

    /**
     * @var string
     *
     * @ORM\Column(name="rfc", type="string", length=50, nullable=true)
     */
    private $rfc;

    /**
     * @var string
     *
     * @ORM\Column(name="direccionfiscal", type="string", length=255, nullable=true)
     */
    private $direccionfiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="correofacturacion", type="string", length=255, nullable=true)
     */
    private $correofacturacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Barco", mappedBy="cliente",cascade={"persist"})
     */
    private $barcos;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", mappedBy="cliente")
     */
    private $mhcotizaciones;

    public function __construct() {
        $this->barcos = new ArrayCollection();
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
     * @return Cliente
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
     * Set correo
     *
     * @param string $correo
     *
     * @return Cliente
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Cliente
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Cliente
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set celular
     *
     * @param string $celular
     *
     * @return Cliente
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Cliente
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

    /**
     * Set fecharegistro
     *
     * @param \DateTime $fecharegistro
     *
     * @return Cliente
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
     * Set horaregistro
     *
     * @param \DateTime $horaregistro
     *
     * @return Cliente
     */
    public function setHoraregistro($horaregistro)
    {
        $this->horaregistro = $horaregistro;

        return $this;
    }

    /**
     * Get horaregistro
     *
     * @return \DateTime
     */
    public function getHoraregistro()
    {
        return $this->horaregistro;
    }

    /**
     * Set empresa
     *
     * @param string $empresa
     *
     * @return Cliente
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return string
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set razonsocial
     *
     * @param string $razonsocial
     *
     * @return Cliente
     */
    public function setRazonsocial($razonsocial)
    {
        $this->razonsocial = $razonsocial;

        return $this;
    }

    /**
     * Get razonsocial
     *
     * @return string
     */
    public function getRazonsocial()
    {
        return $this->razonsocial;
    }

    /**
     * Set rfc
     *
     * @param string $rfc
     *
     * @return Cliente
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
     * Set direccionfiscal
     *
     * @param string $direccionfiscal
     *
     * @return Cliente
     */
    public function setDireccionfiscal($direccionfiscal)
    {
        $this->direccionfiscal = $direccionfiscal;

        return $this;
    }

    /**
     * Get direccionfiscal
     *
     * @return string
     */
    public function getDireccionfiscal()
    {
        return $this->direccionfiscal;
    }

    /**
     * Set correofacturacion
     *
     * @param string $correofacturacion
     *
     * @return Cliente
     */
    public function setCorreofacturacion($correofacturacion)
    {
        $this->correofacturacion = $correofacturacion;

        return $this;
    }

    /**
     * Get correofacturacion
     *
     * @return string
     */
    public function getCorreofacturacion()
    {
        return $this->correofacturacion;
    }

    /**
     * Set estatus
     *
     * @param boolean $estatus
     *
     * @return Cliente
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
     * Add barco
     *
     * @param \AppBundle\Entity\Barco $barco
     *
     * @return Cliente
     */
    public function addBarco(\AppBundle\Entity\Barco $barco)
    {
        $barco->setCliente($this);
        //$this->barcos->add($barco);
        $this->barcos[] = $barco;

        return $this;
    }

    /**
     * Remove barco
     *
     * @param \AppBundle\Entity\Barco $barco
     */
    public function removeBarco(\AppBundle\Entity\Barco $barco)
    {
        $this->barcos->removeElement($barco);
    }

    /**
     * Get barcos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBarcos()
    {
        return $this->barcos;
    }

    /**
     * Add marinahumedacotizacion
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return Cliente
     */
    public function addMarinaHumedaCotizacion(\AppBundle\Entity\MarinaHumedaCotizacion $marinahumedacotizacion)
    {
        $marinahumedacotizacion->setCliente($this);
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
     * @return Cliente
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

<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Cliente\Notificacion;
use AppBundle\Entity\Cliente\RazonSocial;
use AppBundle\Entity\Cliente\Reporte;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClienteRepository")
 * @UniqueEntity("correo", message="Este correo ya ha sido registrado")
 */
class Cliente implements UserInterface, \Serializable
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
     * @Groups({"currentOcupation", "cotizaciones"})
     *
     * @Assert\NotBlank(message="Nombre no puede quedar vacío")
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @Groups({"cotizaciones"})
     *
     * @Assert\NotBlank(message="Correo no puede quedar vacío")
     * @Assert\Email(message = "El correo '{{ value }}' no es válido.")
     * @Assert\Regex(
     *     pattern="/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD",
     *     message="Este correo no es valido"
     * )
     *
     * @ORM\Column(name="correo", type="string", length=255, unique=true)
     */
    private $correo;

    /**
     * @var string
     *
     * @Groups({"cotizaciones"})
     *
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Error, número de teléfono no válido"
     * )
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @Groups({"cotizaciones"})
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var int
     *
     * @ORM\Column(name="idioma", type="smallint")
     */
    private $idioma;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     * @var integer
     *
     * @ORM\Column(name="monederomarinahumeda", type="bigint", nullable=true)
     */
    private $monederomarinahumeda;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text")
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="estatus", type="boolean")
     */
    private $estatus;

    /**
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Barco", mappedBy="cliente",cascade={"persist", "remove"})
     */
    private $barcos;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MonederoMovimiento", mappedBy="cliente")
     */
    private $monederomovimientos;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", mappedBy="cliente")
     */
    private $mhcotizaciones;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacionAdicional", mappedBy="cliente")
     */
    private $mhcotizacionesadicionales;

    /**
     * @var AstilleroCotizacion
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AstilleroCotizacion", mappedBy="cliente")
     */
    private $astilleroCotizaciones;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaSolicitudGasolina", mappedBy="cliente")
     */
    private $appgasolinasolicitudes;

    /**
     * @var RazonSocial
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cliente\RazonSocial", mappedBy="cliente", cascade={"persist", "remove"})
     */
    private $razonesSociales;

    /**
     * @var Reporte
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cliente\Reporte", mappedBy="cliente", cascade={"persist", "remove"})
     */
    private $reportes;

    /**
     * @var Notificacion
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cliente\Notificacion", mappedBy="cliente")
     */
    private $notificaciones;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Combustible", mappedBy="cliente")
     */
    private $combustibles;

    public function __construct() {
        $this->barcos = new ArrayCollection();
        $this->monederomovimientos = new ArrayCollection();
        $this->mhcotizaciones = new ArrayCollection();
        $this->mhcotizacionesadicionales = new ArrayCollection();
        $this->razonesSociales = new ArrayCollection();
        $this->astilleroCotizaciones = new ArrayCollection();
        $this->appgasolinasolicitudes = new ArrayCollection();
        $this->reportes = new ArrayCollection();
        $this->notificaciones = new ArrayCollection();
        $this->combustibles = new ArrayCollection();
        $this->idioma = 1;
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
     * set password
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
     *
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param Barco $barco
     *
     * @return Cliente
     */
    public function addBarco(Barco $barco)
    {
        $barco->setCliente($this);

        $this->barcos[] = $barco;

        return $this;
    }

    /**
     * Remove barco
     *
     * @param Barco $barco
     */
    public function removeBarco(Barco $barco)
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
     * @param MarinaHumedaCotizacion $marinahumedacotizacion
     *
     * @return Cliente
     */
    public function addMarinaHumedaCotizacion(MarinaHumedaCotizacion $marinahumedacotizacion)
    {
        $marinahumedacotizacion->setCliente($this);
        $this->mhcotizaciones[] = $marinahumedacotizacion;
        return $this;
    }

    /**
     * Remove marinahumedacotizacion
     *
     * @param MarinaHumedaCotizacion $marinahumedacotizacion
     */
    public function removeMarinaHumedaCotizacion(MarinaHumedaCotizacion $marinahumedacotizacion)
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
     * @param MarinaHumedaCotizacion $mhcotizacione
     *
     * @return Cliente
     */
    public function addMhcotizacione(MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones[] = $mhcotizacione;

        return $this;
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
     * Add monederomovimiento
     *
     * @param MonederoMovimiento $monederomovimiento
     *
     * @return Cliente
     */
    public function addMonederomovimiento(MonederoMovimiento $monederomovimiento)
    {
        $this->monederomovimientos[] = $monederomovimiento;

        return $this;
    }

    /**
     * Remove monederomovimiento
     *
     * @param MonederoMovimiento $monederomovimiento
     */
    public function removeMonederomovimiento(MonederoMovimiento $monederomovimiento)
    {
        $this->monederomovimientos->removeElement($monederomovimiento);
    }

    /**
     * Get monederomovimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMonederomovimientos()
    {
        return $this->monederomovimientos;
    }

    /**
     * @return int
     */
    public function getMonederomarinahumeda()
    {
        return $this->monederomarinahumeda;
    }

    /**
     * @param int $monederomarinahumeda
     */
    public function setMonederomarinahumeda($monederomarinahumeda)
    {
        $this->monederomarinahumeda = $monederomarinahumeda;
    }

    /**
     * Add razonesSociale
     *
     * @param RazonSocial $razonesSociale
     *
     * @return Cliente
     */
    public function addRazonesSociale(RazonSocial $razonesSociale)
    {
        $razonesSociale->setCliente($this);
        $this->razonesSociales[] = $razonesSociale;

        return $this;
    }

    /**
     * Remove razonesSociale
     *
     * @param RazonSocial $razonesSociale
     */
    public function removeRazonesSociale(RazonSocial $razonesSociale)
    {
        $this->razonesSociales->removeElement($razonesSociale);
    }

    /**
     * Get razonesSociales
     *
     * @return RazonSocial
     */
    public function getRazonesSociales()
    {
        return $this->razonesSociales;
    }

    /**
     * Remove mhcotizacione
     *
     * @param MarinaHumedaCotizacion $mhcotizacione
     */
    public function removeMhcotizacione(MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones->removeElement($mhcotizacione);
    }

    /**
     * Add mhcotizacionesadicionale
     *
     * @param MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale
     *
     * @return Cliente
     */
    public function addMhcotizacionesadicionale(MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale)
    {
        $this->mhcotizacionesadicionales[] = $mhcotizacionesadicionale;

        return $this;
    }

    /**
     * Remove mhcotizacionesadicionale
     *
     * @param MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale
     */
    public function removeMhcotizacionesadicionale(MarinaHumedaCotizacionAdicional $mhcotizacionesadicionale)
    {
        $this->mhcotizacionesadicionales->removeElement($mhcotizacionesadicionale);
    }

    /**
     * Add astilleroCotizacione
     *
     * @param AstilleroCotizacion $astilleroCotizacione
     *
     * @return Cliente
     */
    public function addAstilleroCotizacione(AstilleroCotizacion $astilleroCotizacione)
    {
        $this->astilleroCotizaciones[] = $astilleroCotizacione;

        return $this;
    }

    /**
     * Remove astilleroCotizacione
     *
     * @param AstilleroCotizacion $astilleroCotizacione
     */
    public function removeAstilleroCotizacione(AstilleroCotizacion $astilleroCotizacione)
    {
        $this->astilleroCotizaciones->removeElement($astilleroCotizacione);
    }

    /**
     * Get astilleroCotizaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAstilleroCotizaciones()
    {
        return $this->astilleroCotizaciones;
    }

    /**
     * Set idioma.
     *
     * @param int $idioma
     *
     * @return Cliente
     */
    public function setIdioma($idioma)
    {
        $this->idioma = $idioma;

        return $this;
    }

    /**
     * Get idioma.
     *
     * @return int
     */
    public function getIdioma()
    {
        return $this->idioma;
    }

    /**
     * Add appgasolinasolicitude.
     *
     * @param MarinaHumedaSolicitudGasolina $appgasolinasolicitude
     *
     * @return Cliente
     */
    public function addAppgasolinasolicitude(MarinaHumedaSolicitudGasolina $appgasolinasolicitude)
    {
        $this->appgasolinasolicitudes[] = $appgasolinasolicitude;

        return $this;
    }

    /**
     * Remove appgasolinasolicitude.
     *
     * @param MarinaHumedaSolicitudGasolina $appgasolinasolicitude
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAppgasolinasolicitude(MarinaHumedaSolicitudGasolina $appgasolinasolicitude)
    {
        return $this->appgasolinasolicitudes->removeElement($appgasolinasolicitude);
    }

    /**
     * Get appgasolinasolicitudes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppgasolinasolicitudes()
    {
        return $this->appgasolinasolicitudes;
    }

    /**
     * Add reporte.
     *
     * @param Reporte $reporte
     *
     * @return Cliente
     */
    public function addReporte(Reporte $reporte)
    {
        $this->reportes[] = $reporte;

        return $this;
    }

    /**
     * Remove reporte.
     *
     * @param Reporte $reporte
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeReporte(Reporte $reporte)
    {
        return $this->reportes->removeElement($reporte);
    }

    /**
     * Get reportes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportes()
    {
        return $this->reportes;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_CLIENTS'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getNombre();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->nombre,
            $this->password,
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->nombre,
            $this->password,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * Add notificacione.
     *
     * @param Notificacion $notificacione
     *
     * @return Cliente
     */
    public function addNotificacione(Notificacion $notificacione)
    {
        $this->notificaciones[] = $notificacione;

        return $this;
    }

    /**
     * Remove notificacione.
     *
     * @param Notificacion $notificacione
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNotificacione(Notificacion $notificacione)
    {
        return $this->notificaciones->removeElement($notificacione);
    }

    /**
     * Get notificaciones.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }

    /**
     * Add combustible.
     *
     * @param \AppBundle\Entity\Combustible $combustible
     *
     * @return Cliente
     */
    public function addCombustible(\AppBundle\Entity\Combustible $combustible)
    {
        $this->combustibles[] = $combustible;

        return $this;
    }

    /**
     * Remove combustible.
     *
     * @param \AppBundle\Entity\Combustible $combustible
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCombustible(\AppBundle\Entity\Combustible $combustible)
    {
        return $this->combustibles->removeElement($combustible);
    }

    /**
     * Get combustibles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCombustibles()
    {
        return $this->combustibles;
    }
}

<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proveedor
 *
 * @ORM\Table(name="astillero_proveedor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ProveedorRepository")
 */
class Proveedor implements UserInterface, \Serializable
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
     *
     * @ORM\Column(name="razonsocial", type="string", length=255, nullable=true)
     */
    private $razonsocial;

    /**
     * @var float
     * @Assert\NotBlank(
     *     message="Porcentaje no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="porcentaje", type="float")
     */
    private $porcentaje;

    /**
     * @var int Estatus: 0 Externo, 1 Interno
     *
     * @ORM\Column(name="tipo", type="smallint")
     */
    private $tipo;

    /**
     * @var int Estatus: 0 Proveedor, 1 Contratista
     *
     * @ORM\Column(name="proveedorcontratista", type="smallint")
     */
    private $proveedorcontratista;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Correo no puede quedar vacío"
     * )
     *
     * @Assert\Email(
     *     message = "El correo '{{ value }}' no es válido."
     * )
     *
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
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Astillero\Contratista", mappedBy="proveedor")
     */
    private $AContratistas;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Astillero\Proveedor\Banco", mappedBy="proveedor",cascade={"persist"})
     */
    private $Bancos;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Astillero\Proveedor\Trabajo")
     * @ORM\JoinTable(name="proveedores_trabajos",
     *      joinColumns={@ORM\JoinColumn(name="idproveedor", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idtrabajo", referencedColumnName="id")}
     *      )
     */
    private $Trabajos;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     * @ORM\JoinColumn(name="idempresa", referencedColumnName="id")
     */
    private $empresa;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->AContratistas = new ArrayCollection();
        $this->Bancos = new ArrayCollection();
    }

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
     * @return Proveedor
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
     * Set razonsocial.
     *
     * @param string $razonsocial
     *
     * @return Proveedor
     */
    public function setRazonsocial($razonsocial)
    {
        $this->razonsocial = $razonsocial;

        return $this;
    }

    /**
     * Get razonsocial.
     *
     * @return string
     */
    public function getRazonsocial()
    {
        return $this->razonsocial;
    }

    /**
     * Set porcentaje.
     *
     * @param float $porcentaje
     *
     * @return Proveedor
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje.
     *
     * @return float
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     *
     * @return Proveedor
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Add aContratista.
     *
     * @param Contratista $aContratista
     *
     * @return Proveedor
     */
    public function addAContratista(Contratista $aContratista)
    {
        $this->AContratistas[] = $aContratista;

        return $this;
    }

    /**
     * Remove aContratista.
     *
     * @param Contratista $aContratista
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAContratista(Contratista $aContratista)
    {
        return $this->AContratistas->removeElement($aContratista);
    }

    /**
     * Get aContratistas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAContratistas()
    {
        return $this->AContratistas;
    }

    /**
     * Add banco.
     *
     * @param Proveedor\Banco $banco
     *
     * @return Proveedor
     */
    public function addBanco(Proveedor\Banco $banco)
    {
        $banco->setProveedor($this);
        $this->Bancos[] = $banco;

        return $this;
    }

    /**
     * Remove banco.
     *
     * @param Proveedor\Banco $banco
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBanco(Proveedor\Banco $banco)
    {
        return $this->Bancos->removeElement($banco);
    }

    /**
     * Get bancos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBancos()
    {
        return $this->Bancos;
    }


    /**
     * Add trabajo.
     *
     * @param Proveedor\Trabajo $trabajo
     *
     * @return Proveedor
     */
    public function addTrabajo(Proveedor\Trabajo $trabajo)
    {
        $this->Trabajos[] = $trabajo;

        return $this;
    }

    /**
     * Remove trabajo.
     *
     * @param Proveedor\Trabajo $trabajo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTrabajo(Proveedor\Trabajo $trabajo)
    {
        return $this->Trabajos->removeElement($trabajo);
    }

    /**
     * Get trabajos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrabajos()
    {
        return $this->Trabajos;
    }

    /**
     * Set correo.
     *
     * @param string $correo
     *
     * @return Proveedor
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo.
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set telefono.
     *
     * @param string|null $telefono
     *
     * @return Proveedor
     */
    public function setTelefono($telefono = null)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono.
     *
     * @return string|null
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set rfc.
     *
     * @param string $rfc
     *
     * @return Proveedor
     */
    public function setRfc($rfc)
    {
        $this->rfc = $rfc;

        return $this;
    }

    /**
     * Get rfc.
     *
     * @return string
     */
    public function getRfc()
    {
        return $this->rfc;
    }

    /**
     * Set direccionfiscal.
     *
     * @param string $direccionfiscal
     *
     * @return Proveedor
     */
    public function setDireccionfiscal($direccionfiscal)
    {
        $this->direccionfiscal = $direccionfiscal;

        return $this;
    }

    /**
     * Get direccionfiscal.
     *
     * @return string
     */
    public function getDireccionfiscal()
    {
        return $this->direccionfiscal;
    }

    /**
     * Set proveedorcontratista.
     *
     * @param int $proveedorcontratista
     *
     * @return Proveedor
     */
    public function setProveedorcontratista($proveedorcontratista)
    {
        $this->proveedorcontratista = $proveedorcontratista;

        return $this;
    }

    /**
     * Get proveedorcontratista.
     *
     * @return int
     */
    public function getProveedorcontratista()
    {
        return $this->proveedorcontratista;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_SUPPLIERS'];
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
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->nombre;
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
        return serialize(array(
            $this->id,
            $this->nombre,
            $this->password,
        ));
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Proveedor
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     *
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
     * Set empresa.
     *
     * @param \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null $empresa
     *
     * @return Proveedor
     */
    public function setEmpresa(\AppBundle\Entity\Contabilidad\Facturacion\Emisor $empresa = null)
    {
        $this->empresa = $empresa;
        return $this;
    }

    /**
     * Get empresa.
     *
     * @return \AppBundle\Entity\Contabilidad\Facturacion\Emisor|null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }
}

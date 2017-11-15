<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 */
class Usuario implements AdvancedUserInterface, \Serializable
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
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=50, unique=true)
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro", type="datetime")
     */
    private $registro;

    /**
     * @var int
     *
     * @ORM\Column(name="estatus", type="smallint")
     */
    private $estatus;

    /**
     * @var Rol
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rol", inversedBy="users")
     */
    private $rol;

    public function __construct()
    {
        $this->estatus = 1;
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
     * @return Usuario
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
     * @return Usuario
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
     * @return Usuario
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
     * Set registro
     *
     * @param \DateTime $registro
     *
     * @return Usuario
     */
    public function setRegistro($registro)
    {
        $this->registro = $registro;

        return $this;
    }

    /**
     * Get registro
     *
     * @return \DateTime
     */
    public function getRegistro()
    {
        return $this->registro;
    }

    /**
     * Set estatus
     *
     * @param integer $estatus
     *
     * @return Usuario
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return int
     */
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set rol
     *
     * @param Rol $rol
     *
     * @return Usuario
     */
    public function setRol(Rol $rol = null)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Checa si la cuenta del usuario ya expiro
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checa si la cuenta del usuario esta bloqueada
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checa si el password del usuario expiró
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checa si el usuario esta activo
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->estatus === 0 ? false : true;
    }

    /**
     * Serializacion del usuario
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->nombre,
            $this->correo,
            $this->password,
            $this->estatus
        ]);
    }

    /**
     * Deserializacion del usuario
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->nombre,
            $this->correo,
            $this->password,
            $this->estatus
            ) = unserialize($serialized);
    }

    public function getUsername()
    {
        return $this->nombre;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return Rol|array
     */
    public function getRoles()
    {
        return [
            'ROLE_USER',
            'ROLE_ADMIN'
        ];
    }

    public function eraseCredentials()
    {
    }
}

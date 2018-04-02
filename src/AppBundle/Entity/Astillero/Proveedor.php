<?php

namespace AppBundle\Entity\Astillero;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proveedor
 *
 * @ORM\Table(name="astillero_proveedor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\ProveedorRepository")
 */
class Proveedor
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
     * @Assert\NotBlank(
     *     message="Razón social no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="razonsocial", type="string", length=255)
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
     * @Assert\Regex(
     *     pattern="/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/",
     *     message="El RFC es invalido"
     *     )
     *
     * @ORM\Column(name="rfc", type="string", length=50)
     */
    private $rfc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="direccionfiscal", type="string", length=255)
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


    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->AContratistas = new ArrayCollection();
        $this->Bancos = new ArrayCollection();
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
     * @param \AppBundle\Entity\Astillero\Contratista $aContratista
     *
     * @return Proveedor
     */
    public function addAContratista(\AppBundle\Entity\Astillero\Contratista $aContratista)
    {
        $this->AContratistas[] = $aContratista;

        return $this;
    }

    /**
     * Remove aContratista.
     *
     * @param \AppBundle\Entity\Astillero\Contratista $aContratista
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAContratista(\AppBundle\Entity\Astillero\Contratista $aContratista)
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
     * @param \AppBundle\Entity\Astillero\Proveedor\Banco $banco
     *
     * @return Proveedor
     */
    public function addBanco(\AppBundle\Entity\Astillero\Proveedor\Banco $banco)
    {
        $banco->setProveedor($this);
        $this->Bancos[] = $banco;

        return $this;
    }

    /**
     * Remove banco.
     *
     * @param \AppBundle\Entity\Astillero\Proveedor\Banco $banco
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBanco(\AppBundle\Entity\Astillero\Proveedor\Banco $banco)
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
     * @param \AppBundle\Entity\Astillero\Proveedor\Trabajo $trabajo
     *
     * @return Proveedor
     */
    public function addTrabajo(\AppBundle\Entity\Astillero\Proveedor\Trabajo $trabajo)
    {
        $this->Trabajos[] = $trabajo;

        return $this;
    }

    /**
     * Remove trabajo.
     *
     * @param \AppBundle\Entity\Astillero\Proveedor\Trabajo $trabajo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTrabajo(\AppBundle\Entity\Astillero\Proveedor\Trabajo $trabajo)
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
}

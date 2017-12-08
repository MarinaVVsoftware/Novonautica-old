<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Embarcacion
 *
 * @ORM\Table(name="embarcacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionRepository")
 *
 * @Vich\Uploadable
 *
 * @ORM\HasLifecycleCallbacks()
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
     * @var int $precio esta guardado en centavos
     *
     * @ORM\Column(name="precio", type="bigint")
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
     * @var string
     *
     * @ORM\Column(name="builder", type="string", length=50, nullable=true)
     */
    private $builder;

    /**
     * @var string
     *
     * @ORM\Column(name="interior_designer", type="string", length=50, nullable=true)
     */
    private $interiorDesigner;

    /**
     * @var string
     *
     * @ORM\Column(name="exterior_designer", type="string", length=50, nullable=true)
     */
    private $exteriorDesigner;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", nullable=true)
     */
    private $video;

    /**
     * @var array
     *
     * @ORM\Column(name="motores", type="array", nullable=true)
     */
    private $motores;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $brochure;

    /**
     * @var File
     *
     * @Assert\File(mimeTypes={"application/pdf", "image/*"}, mimeTypesMessage="Solo se permiten archivos PDF e imagenes")
     *
     * @Vich\UploadableField(mapping="embarcacion_brochure", fileNameProperty="brochure")
     */
    private $brochureFile;

    /**
     * @var EmbarcacionMarca
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmbarcacionMarca")
     */
    private $marca;

    /**
     * @var EmbarcacionModelo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmbarcacionModelo")
     */
    private $modelo;

    /**
     * @var Pais
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pais")
     */
    private $pais;

    /**
     * @var EmbarcacionImagen
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EmbarcacionImagen", mappedBy="embarcacion", cascade={"persist", "remove"})
     */
    private $imagenes;

    /**
     * @var EmbarcacionLayout
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EmbarcacionLayout", mappedBy="embarcacion", cascade={"persist", "remove"})
     */
    private $layouts;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->updateAt = new \DateTimeImmutable();
        $this->imagenes = new ArrayCollection();
        $this->layouts = new ArrayCollection();
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
     * @return string
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param string $builder
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return string
     */
    public function getInteriorDesigner()
    {
        return $this->interiorDesigner;
    }

    /**
     * @param string $interiorDesigner
     */
    public function setInteriorDesigner($interiorDesigner)
    {
        $this->interiorDesigner = $interiorDesigner;
    }

    /**
     * @return string
     */
    public function getExteriorDesigner()
    {
        return $this->exteriorDesigner;
    }

    /**
     * @param string $exteriorDesigner
     */
    public function setExteriorDesigner($exteriorDesigner)
    {
        $this->exteriorDesigner = $exteriorDesigner;
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

    /**
     * @return EmbarcacionModelo
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * @param EmbarcacionModelo $modelo
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param string $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * Add imagene
     *
     * @param EmbarcacionImagen $imagene
     *
     * @return Embarcacion
     */
    public function addImagene(EmbarcacionImagen $imagene)
    {
        $imagene->setEmbarcacion($this);
        $this->imagenes[] = $imagene;

        return $this;
    }

    /**
     * Remove imagene
     *
     * @param EmbarcacionImagen $imagene
     */
    public function removeImagene(EmbarcacionImagen $imagene)
    {
        $this->imagenes->removeElement($imagene);
    }

    /**
     * Get imagenes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImagenes()
    {
        return $this->imagenes;
    }

    /**
     * Add layout
     *
     * @param EmbarcacionLayout $layout
     *
     * @return Embarcacion
     */
    public function addLayout(EmbarcacionLayout $layout)
    {
        $layout->setEmbarcacion($this);
        $this->layouts[] = $layout;

        return $this;
    }

    /**
     * Remove layout
     *
     * @param EmbarcacionLayout $layout
     */
    public function removeLayout(EmbarcacionLayout $layout)
    {
        $this->layouts->removeElement($layout);
    }

    /**
     * Get layouts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @return string|null
     */
    public function getBrochure()
    {
        return $this->brochure;
    }

    /**
     * @param mixed $brochure
     */
    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;
    }

    /**
     * @return File|null
     */
    public function getBrochureFile()
    {
        return $this->brochureFile;
    }

    /**
     * @param File $brochureFile
     *
     * @return Embarcacion
     */
    public function setBrochureFile(File $brochureFile = null)
    {
        $this->brochureFile = $brochureFile;

        if ($brochureFile) {
            $this->updateAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Set motores
     *
     * @param array $motores
     *
     * @return Embarcacion
     */
    public function setMotores($motores)
    {
        $this->motores = array_values($motores);

        return $this;
    }

    /**
     * Get motores
     *
     * @return array
     */
    public function getMotores()
    {
        return $this->motores;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Embarcacion
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set pais
     *
     * @param Pais $pais
     *
     * @return Embarcacion
     */
    public function setPais(Pais $pais = null)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return Pais
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     */
    public function validateConstruccion(ExecutionContextInterface $context)
    {
        if ($this->getConstruccion() === 'custom') {
            if (!$this->getBuilder()) {
                $context->buildViolation('No dejes este dato vacio.')
                    ->atPath('builder')
                    ->addViolation();
            }
            if (!$this->getInteriorDesigner()) {
                $context->buildViolation('No dejes este dato vacio.')
                    ->atPath('interiorDesigner')
                    ->addViolation();
            }
            if (!$this->getExteriorDesigner()) {
                $context->buildViolation('No dejes este dato vacio.')
                    ->atPath('exteriorDesigner')
                    ->addViolation();
            }
        } else {
            if (!$this->getMarca()) {
                $context->buildViolation('Por favor elige un valor.')
                    ->atPath('marca')
                    ->addViolation();
            }
            if (!$this->getModelo()) {
                $context->buildViolation('Por favor elige un valor.')
                    ->atPath('modelo')
                    ->addViolation();
            }
        }
    }
}

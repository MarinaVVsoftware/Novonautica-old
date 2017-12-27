<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Producto\Categoria;
use AppBundle\Entity\Producto\Marca;
use AppBundle\Entity\Producto\Subcategoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductoRepository")
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class Producto
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
     * @Assert\NotBlank(message="Este campo no puede quedar vacio")
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Este campo no puede quedar vacio")
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="ucp", type="string", length=50, nullable=true)
     */
    private $ucp;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede quedar vacio")
     *
     * @ORM\Column(name="modelo", type="string", length=50)
     */
    private $modelo;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede quedar vacio")
     *
     * @ORM\Column(name="unidad", type="string", length=20)
     */
    private $unidad;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Este campo no puede quedar vacio")
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagen;

    /**
     * @var File
     *
     * @Assert\File(mimeTypes={"image/*"}, mimeTypesMessage="Solo se permiten imagenes")
     *
     * @Vich\UploadableField(mapping="producto_imagen", fileNameProperty="imagen")
     */
    private $imagenFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fichaTecnica;

    /**
     * @var File
     *
     * @Assert\File(mimeTypes={"application/pdf", "image/*"}, mimeTypesMessage="Solo se permiten archivos PDF e imagenes")
     *
     * @Vich\UploadableField(mapping="producto_ficha", fileNameProperty="fichaTecnica")
     */
    private $fichaTecnicaFile;

    /**
     * @var Marca
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto\Marca", inversedBy="productos")
     */
    private $marca;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto\Categoria", inversedBy="productos")
     */
    private $categoria;

    /**
     * @var Subcategoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto\Subcategoria", inversedBy="productos")
     */
    private $subcategoria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    public function __construct()
    {
        $this->updateAt = new \DateTimeImmutable();
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
     * @return Producto
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
     * @return Producto
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
     * Set ucp
     *
     * @param string $ucp
     *
     * @return Producto
     */
    public function setUcp($ucp)
    {
        $this->ucp = $ucp;

        return $this;
    }

    /**
     * Get ucp
     *
     * @return string
     */
    public function getUcp()
    {
        return $this->ucp;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     *
     * @return Producto
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
     * Set unidad
     *
     * @param string $unidad
     *
     * @return Producto
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Producto
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
     * Set imagen
     *
     * @param string $imagen
     *
     * @return Producto
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * @param File|UploadedFile $image
     *
     * @return Producto
     */
    public function setImagenFile(File $image = null)
    {
        $this->imagenFile = $image;

        if ($image) {
            $this->updateAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImagenFile()
    {
        return $this->imagenFile;
    }

    /**
     * Set fichaTecnica
     *
     * @param string $fichaTecnica
     *
     * @return Producto
     */
    public function setFichaTecnica($fichaTecnica)
    {
        $this->fichaTecnica = $fichaTecnica;

        return $this;
    }

    /**
     * Get fichaTecnica
     *
     * @return string
     */
    public function getFichaTecnica()
    {
        return $this->fichaTecnica;
    }

    /**
     * @param File $fichaTecnicaFile
     *
     * @return Producto
     */
    public function setFichaTecnicaFile(File $fichaTecnicaFile = null)
    {
        $this->fichaTecnicaFile = $fichaTecnicaFile;

        if ($fichaTecnicaFile) {
            $this->updateAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFichaTecnicaFile()
    {
        return $this->fichaTecnicaFile;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Producto
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
     * Set marca
     *
     * @param Marca $marca
     *
     * @return Producto
     */
    public function setMarca(Marca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return Marca
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set categoria
     *
     * @param Categoria $categoria
     *
     * @return Producto
     */
    public function setCategoria(Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set subcategoria
     *
     * @param Subcategoria $subcategoria
     *
     * @return Producto
     */
    public function setSubcategoria(Subcategoria $subcategoria = null)
    {
        $this->subcategoria = $subcategoria;

        return $this;
    }

    /**
     * Get subcategoria
     *
     * @return Subcategoria
     */
    public function getSubcategoria()
    {
        return $this->subcategoria;
    }
}

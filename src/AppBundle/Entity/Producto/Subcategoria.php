<?php

namespace AppBundle\Entity\Producto;

use AppBundle\Entity\Producto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Subcategoria
 *
 * @ORM\Table(name="producto_subcategoria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Producto\SubcategoriaRepository")
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class Subcategoria
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagen;

    /**
     * @var File
     *
     * @Assert\File(mimeTypes={"image/*"}, mimeTypesMessage="Solo se permiten imagenes")
     *
     * @Vich\UploadableField(mapping="producto_subcategoria_imagen", fileNameProperty="imagen")
     */
    private $imagenFile;

    /**
     * @var Producto
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Producto", mappedBy="subcategoria")
     */
    private $productos;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto\Categoria", inversedBy="subcategorias")
     */
    private $categoria;

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
        $this->productos = new ArrayCollection();
        $this->updateAt = new \DateTimeImmutable();
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
     * @return Subcategoria
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
     * Set imagen
     *
     * @param string $imagen
     *
     * @return Subcategoria
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
     * @return Subcategoria
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
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Subcategoria
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
     * Add producto
     *
     * @param Producto $producto
     *
     * @return Subcategoria
     */
    public function addProducto(Producto $producto)
    {
        $this->productos[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param Producto $producto
     */
    public function removeProducto(Producto $producto)
    {
        $this->productos->removeElement($producto);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductos()
    {
        return $this->productos;
    }

    /**
     * Set categoria
     *
     * @param Categoria $categoria
     *
     * @return Subcategoria
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
}

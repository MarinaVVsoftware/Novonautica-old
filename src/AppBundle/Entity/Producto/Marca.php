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
 * Marca
 *
 * @ORM\Table(name="producto_marca")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Producto\MarcaRepository")
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class Marca
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagen;

    /**
     * @var File
     *
     * @Assert\File(
     *     mimeTypes={"image/*"}, mimeTypesMessage="Solo se permiten imagenes",
     *     maxSize="2M", maxSizeMessage="El tamaño maximo son 2MB"
     * )
     *
     * @Vich\UploadableField(mapping="producto_marca_imagen", fileNameProperty="imagen")
     */
    private $imagenFile;

    /**
     * @var Producto
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Producto", mappedBy="marca")
     */
    private $productos;

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
     * @return Marca
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
     * @return Marca
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
     * @return Marca
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
     * @return Marca
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
     * @return Marca
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
}

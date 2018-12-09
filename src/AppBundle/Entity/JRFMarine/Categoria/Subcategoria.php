<?php

namespace AppBundle\Entity\JRFMarine\Categoria;

use AppBundle\Entity\JRFMarine\Categoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Subcategoria
 *
 * @ORM\Table(name="j_r_f_marine_categoria_subcategoria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JRFMarine\Categoria\SubcategoriaRepository")
 * @Vich\Uploadable
 */
class Subcategoria implements \JsonSerializable
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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", nullable=true)
     */
    private $imagen;

    /**
     * @var File
     *
     * @Assert\Image()
     *
     * @Vich\UploadableField(
     *     mapping="jrf_producto_subcategoria",
     *     fileNameProperty="imagen"
     * )
     */
    private $imagenFile;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JRFMarine\Categoria")
     */
    private $categoria;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $updateAt;

    public function __construct()
    {
        $this->updateAt = new \DateTime();
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
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
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
     * Set imagen.
     *
     * @param string|null $imagen
     */
    public function setImagen($imagen = null)
    {
        $this->imagen = $imagen;
    }

    /**
     * Get imagen.
     *
     * @return string|null
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * @return File
     */
    public function getImagenFile()
    {
        return $this->imagenFile;
    }

    /**
     * @param File $imagenFile
     */
    public function setImagenFile(File $imagenFile = null)
    {
        $this->imagenFile = $imagenFile;

        if (null !== $imagenFile) {
            $this->updateAt = new \DateTime();
        }
    }

    /**
     * @return Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param Categoria $categoria
     */
    public function setCategoria(Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    /**
     * Set updateAt.
     *
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }

    /**
     * Get updateAt.
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
        ];
    }
}

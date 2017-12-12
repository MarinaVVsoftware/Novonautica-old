<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * EmbarcacionMarca
 *
 * @ORM\Table(name="embarcacion_marca")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmbarcacionMarcaRepository")
 * @Vich\Uploadable
 */
class EmbarcacionMarca
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
     * @var File
     *
     * @Assert\File(maxSize="2M", maxSizeMessage="La imagen es demasiado pesado, el tamaño maximo es de 2MB")
     * @Assert\Image(mimeTypesMessage="Estas intentando subir un archivo que no imagen")
     *
     * @Vich\UploadableField(mapping="embarcacion_marca", fileNameProperty="imagen")
     */
    private $imagenFile;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", nullable=true)
     */
    private $imagen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @var EmbarcacionModelo
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EmbarcacionModelo", mappedBy="marca", cascade={"persist", "remove"})
     */
    private $modelos;

    public function __construct()
    {
        $this->modelos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNombre();
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
     * @return EmbarcacionMarca
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
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * @param string $imagen
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
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
        if ($imagenFile) {
            $this->updateAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }

    /**
     * Add modelo
     *
     * @param EmbarcacionModelo $modelo
     *
     * @return EmbarcacionMarca
     */
    public function addModelo(EmbarcacionModelo $modelo)
    {
        $this->modelos[] = $modelo;

        return $this;
    }

    /**
     * Remove modelo
     *
     * @param EmbarcacionModelo $modelo
     */
    public function removeModelo(EmbarcacionModelo $modelo)
    {
        $this->modelos->removeElement($modelo);
    }

    /**
     * Get modelos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelos()
    {
        return $this->modelos;
    }
}

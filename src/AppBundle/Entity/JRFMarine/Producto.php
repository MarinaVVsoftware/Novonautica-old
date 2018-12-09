<?php

namespace AppBundle\Entity\JRFMarine;

use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\JRFMarine\Categoria\Subcategoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Producto
 *
 * @ORM\Table(name="j_r_f_marine_producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JRFMarine\ProductoRepository")
 * @Vich\Uploadable
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
     * @ORM\Column(name="nombre", type="string", length=70)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="precio", type="bigint")
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="unidad", type="string", length=10)
     */
    private $unidad;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barras", type="string", length=50)
     */
    private $codigoBarras;

    /**
     * @var int
     *
     * @ORM\Column(name="existencia", type="integer")
     */
    private $existencia;

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
     *     mapping="jrf_producto_imagen",
     *     fileNameProperty="imagen"
     * )
     */
    private $imagenFile;

    /**
     * @var ClaveProdServ
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ")
     */
    private $claveProdServ;

    /**
     * @var ClaveUnidad
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad")
     */
    private $claveUnidad;

    /**
     * @var Marca
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JRFMarine\Marca")
     */
    private $marca;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JRFMarine\Categoria")
     */
    private $categoria;

    /**
     * @var Subcategoria
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JRFMarine\Categoria\Subcategoria")
     */
    private $subcategoria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $updateAt;

    public function __construct()
    {
        $this->existencia = 0;
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
     * Set precio.
     *
     * @param int $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * Get precio.
     *
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set unidad.
     *
     * @param string $unidad
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
    }

    /**
     * Get unidad.
     *
     * @return string
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set codigoBarras.
     *
     * @param string $codigoBarras
     */
    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;
    }

    /**
     * Get codigoBarras.
     *
     * @return string
     */
    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    /**
     * Set existencia.
     *
     * @param int $existencia
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;
    }

    /**
     * Get existencia.
     *
     * @return int
     */
    public function getExistencia()
    {
        return $this->existencia;
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

    /**
     * Set claveProdServ.
     *
     * @param ClaveProdServ|null $claveProdServ
     */
    public function setClaveProdServ(ClaveProdServ $claveProdServ = null)
    {
        $this->claveProdServ = $claveProdServ;
    }

    /**
     * Get claveProdServ.
     *
     * @return ClaveProdServ|null
     */
    public function getClaveProdServ()
    {
        return $this->claveProdServ;
    }

    /**
     * Set claveUnidad.
     *
     * @param ClaveUnidad|null $claveUnidad
     */
    public function setClaveUnidad(ClaveUnidad $claveUnidad = null)
    {
        $this->claveUnidad = $claveUnidad;
    }

    /**
     * Get claveUnidad.
     *
     * @return ClaveUnidad|null
     */
    public function getClaveUnidad()
    {
        return $this->claveUnidad;
    }

    /**
     * Set marca.
     *
     * @param Marca|null $marca
     */
    public function setMarca(Marca $marca = null)
    {
        $this->marca = $marca;
    }

    /**
     * Get marca.
     *
     * @return Marca|null
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set categoria.
     *
     * @param Categoria|null $categoria
     */
    public function setCategoria(Categoria $categoria = null)
    {
        $this->categoria = $categoria;
    }

    /**
     * Get categoria.
     *
     * @return Categoria|null
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set subcategoria.
     *
     * @param Subcategoria|null $subcategoria
     */
    public function setSubcategoria(Subcategoria $subcategoria = null)
    {
        $this->subcategoria = $subcategoria;
    }

    /**
     * Get subcategoria.
     *
     * @return Subcategoria|null
     */
    public function getSubcategoria()
    {
        return $this->subcategoria;
    }
}

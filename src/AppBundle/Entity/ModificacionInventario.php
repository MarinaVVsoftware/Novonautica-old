<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModificacionInventario
 *
 * @ORM\Table(name="modificacion_inventario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModificacionInventarioRepository")
 */
class ModificacionInventario
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
     * @ORM\Column(name="comentario", type="string")
     */
    private $comentario;

    /**
     * @ORM\Column(name="conceptos", type="json")
     */
    private $conceptos;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
     */
    private $creador;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct(Emisor $empresa, Usuario $usuario)
    {
        $this->creador = $usuario;
        $this->empresa = $empresa;
        $this->createdAt = new \DateTime();
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
     * Set comentario.
     *
     * @param string $comentario
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

    /**
     * Get comentario.
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set conceptos.
     *
     * @param $conceptos
     */
    public function setConceptos($conceptos)
    {
        $this->conceptos = $conceptos;
    }

    /**
     * Get conceptos.
     */
    public function getConceptos()
    {
        return $this->conceptos;
    }

    /**
     * Set empresa.
     *
     * @param Contabilidad\Facturacion\Emisor|null $empresa
     */
    public function setEmpresa(Contabilidad\Facturacion\Emisor $empresa = null)
    {
        $this->empresa = $empresa;
    }

    /**
     * Get empresa.
     *
     * @return Contabilidad\Facturacion\Emisor|null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @return Usuario
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

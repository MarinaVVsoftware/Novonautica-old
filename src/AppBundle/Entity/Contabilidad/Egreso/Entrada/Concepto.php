<?php

namespace AppBundle\Entity\Contabilidad\Egreso\Entrada;

use AppBundle\Entity\Contabilidad\Facturacion\Emisor;
use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="contabilidad_egreso_entrada_concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Egreso\Entrada\ConceptoRepository")
 */
class Concepto
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
    private $descripcion;

    /**
     * @var Emisor
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Facturacion\Emisor")
     */
    private $empresa;

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
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Set empresa.
     *
     * @param Emisor|null $empresa
     *
     * @return Concepto
     */
    public function setEmpresa(Emisor $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa.
     *
     * @return Emisor|null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }
}

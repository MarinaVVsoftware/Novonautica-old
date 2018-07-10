<?php

namespace AppBundle\Entity\Contabilidad\Egreso;

use AppBundle\Entity\Contabilidad\Egreso;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entrada
 *
 * @ORM\Table(name="contabilidad_egreso_entrada")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Contabilidad\Egreso\EntradaRepository")
 */
class Entrada
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
     * @var int
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\Column(name="importe", type="bigint")
     */
    private $importe;

    /**
     * @var Egreso
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Egreso", inversedBy="entradas")
     */
    private $egreso;

    /**
     * @var Entrada\Concepto
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Egreso\Entrada\Concepto")
     */
    private $concepto;

    /**
     * @var Entrada\Proveedor
     *
     * @Assert\NotNull(message="Este campo no puede estar vacio")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contabilidad\Egreso\Entrada\Proveedor")
     */
    private $proveedor;

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
     * Set importe.
     *
     * @param int $importe
     *
     * @return Entrada
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe.
     *
     * @return int
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set egreso.
     *
     * @param Egreso|null $egreso
     *
     * @return Entrada
     */
    public function setEgreso(Egreso $egreso = null)
    {
        $this->egreso = $egreso;

        return $this;
    }

    /**
     * Get egreso.
     *
     * @return Egreso|null
     */
    public function getEgreso()
    {
        return $this->egreso;
    }

    /**
     * Set concepto.
     *
     * @param Entrada\Concepto|null $concepto
     *
     * @return Entrada
     */
    public function setConcepto(Entrada\Concepto $concepto = null)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto.
     *
     * @return Entrada\Concepto|null
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * Set proveedor.
     *
     * @param Entrada\Proveedor|null $proveedor
     *
     * @return Entrada
     */
    public function setProveedor(Entrada\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return Entrada\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
}

<?php

namespace AppBundle\Entity\Astillero\Proveedor;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banco
 *
 * @ORM\Table(name="astillero_proveedor_banco")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Astillero\Proveedor\BancoRepository")
 */
class Banco
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
     * @ORM\Column(name="numcuenta", type="string", length=255)
     */
    private $numcuenta;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Astillero\Proveedor", inversedBy="Bancos")
     * @ORM\JoinColumn(name="idproveedor", referencedColumnName="id",onDelete="CASCADE")
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Banco
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
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
     * Set numcuenta.
     *
     * @param string $numcuenta
     *
     * @return Banco
     */
    public function setNumcuenta($numcuenta)
    {
        $this->numcuenta = $numcuenta;

        return $this;
    }

    /**
     * Get numcuenta.
     *
     * @return string
     */
    public function getNumcuenta()
    {
        return $this->numcuenta;
    }

    /**
     * Set proveedor.
     *
     * @param \AppBundle\Entity\Astillero\Proveedor|null $proveedor
     *
     * @return Banco
     */
    public function setProveedor(\AppBundle\Entity\Astillero\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor.
     *
     * @return \AppBundle\Entity\Astillero\Proveedor|null
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
}

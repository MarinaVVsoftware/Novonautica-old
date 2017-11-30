<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Slip
 *
 * @ORM\Table(name="slip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SlipRepository")
 */
class Slip
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
     * @ORM\Column(name="num", type="integer", unique=true)
     */
    private $num;

    /**
     * @var float
     *
     * @ORM\Column(name="pies", type="float")
     */
    private $pies;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", mappedBy="slip")
     */
    private $mhcotizaciones;

    /**
     * @var SlipMovimiento
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SlipMovimiento", mappedBy="slip", cascade={"persist", "remove"})
     */
    private $movimientos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mhcotizaciones = new ArrayCollection();
        $this->movimientos = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Num: '.$this->num.' - '.$this->pies.' fts';
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
     * Set num
     *
     * @param integer $num
     *
     * @return Slip
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return int
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set pies
     *
     * @param float $pies
     *
     * @return Slip
     */
    public function setPies($pies)
    {
        $this->pies = $pies;

        return $this;
    }

    /**
     * Get pies
     *
     * @return float
     */
    public function getPies()
    {
        return $this->pies;
    }

    /**
     * Add mhcotizacione
     *
     * @param MarinaHumedaCotizacion $mhcotizacione
     *
     * @return Slip
     */
    public function addMhcotizacione(MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones[] = $mhcotizacione;

        return $this;
    }

    /**
     * Remove mhcotizacione
     *
     * @param MarinaHumedaCotizacion $mhcotizacione
     */
    public function removeMhcotizacione(MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones->removeElement($mhcotizacione);
    }

    /**
     * Get mhcotizaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMhcotizaciones()
    {
        return $this->mhcotizaciones;
    }

    /**
     * Add movimiento
     *
     * @param SlipMovimiento $movimiento
     *
     * @return Slip
     */
    public function addMovimiento(SlipMovimiento $movimiento)
    {
        $this->movimientos[] = $movimiento;

        return $this;
    }

    /**
     * Remove movimiento
     *
     * @param SlipMovimiento $movimiento
     */
    public function removeMovimiento(SlipMovimiento $movimiento)
    {
        $this->movimientos->removeElement($movimiento);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }
}

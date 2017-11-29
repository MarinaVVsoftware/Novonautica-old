<?php

namespace AppBundle\Entity;

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
     * @var int
     *
     * @ORM\Column(name="estatus", type="smallint", nullable=true)
     */
    private $estatus;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarinaHumedaCotizacion", mappedBy="slip")
     */
    private $mhcotizaciones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mhcotizaciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set estatus
     *
     * @param integer $estatus
     *
     * @return Slip
     */
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }

    /**
     * Get estatus
     *
     * @return int
     */
    public function getEstatus()
    {
        return $this->estatus;
    }


    /**
     * Add mhcotizacione
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione
     *
     * @return Slip
     */
    public function addMhcotizacione(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione)
    {
        $this->mhcotizaciones[] = $mhcotizacione;

        return $this;
    }

    /**
     * Remove mhcotizacione
     *
     * @param \AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione
     */
    public function removeMhcotizacione(\AppBundle\Entity\MarinaHumedaCotizacion $mhcotizacione)
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
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ejemplo
 *
 * @ORM\Table(name="ejemplo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EjemploRepository")
 */
class Ejemplo
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
     * @ORM\Column(name="campo1", type="integer", nullable=true)
     */
    private $campo1;

    /**
     * @var int
     *
     * @ORM\Column(name="campo2", type="smallint")
     */
    private $campo2;

    /**
     * @var int
     *
     * @ORM\Column(name="campo3", type="bigint")
     */
    private $campo3;


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
     * Set campo1
     *
     * @param integer $campo1
     *
     * @return Ejemplo
     */
    public function setCampo1($campo1)
    {
        $this->campo1 = $campo1;

        return $this;
    }

    /**
     * Get campo1
     *
     * @return int
     */
    public function getCampo1()
    {
        return $this->campo1;
    }

    /**
     * Set campo2
     *
     * @param integer $campo2
     *
     * @return Ejemplo
     */
    public function setCampo2($campo2)
    {
        $this->campo2 = $campo2;

        return $this;
    }

    /**
     * Get campo2
     *
     * @return int
     */
    public function getCampo2()
    {
        return $this->campo2;
    }

    /**
     * Set campo3
     *
     * @param integer $campo3
     *
     * @return Ejemplo
     */
    public function setCampo3($campo3)
    {
        $this->campo3 = $campo3;

        return $this;
    }

    /**
     * Get campo3
     *
     * @return int
     */
    public function getCampo3()
    {
        return $this->campo3;
    }
}

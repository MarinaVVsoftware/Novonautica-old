<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ValorSistema
 *
 * @ORM\Table(name="valor_sistema")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ValorSistemaRepository")
 */
class ValorSistema
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
     * @ORM\Column(name="dolar", type="integer")
     */
    private $dolar;

    /**
     * @var float
     * @Assert\NotBlank(
     *     message="El iva no puede quedar vacío"
     * )
     *
     * @ORM\Column(name="iva", type="float")
     */
    private $iva;

    /**
     * @var int
     *
     * @ORM\Column(name="folio_marina", type="bigint")
     */
    private $folioMarina;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_correo_marina", type="text")
     */
    private $mensajeCorreoMarina;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_correo_marina_gasolina", type="text")
     */
    private $mensajeCorreoMarinaGasolina;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_habiles_marina_cotizacion", type="integer")
     */
    private $diasHabilesMarinaCotizacion;

    /**
     * Generate a token
     *
     * @return string (100 characters)
     */
    function generaToken($length)
    {
        //$length = 100;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $token;

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
     * @return string
     */
    public function getMensajeCorreoMarina()
    {
        return $this->mensajeCorreoMarina;
    }

    /**
     * @param string $mensajeCorreoMarina
     */
    public function setMensajeCorreoMarina($mensajeCorreoMarina)
    {
        $this->mensajeCorreoMarina = $mensajeCorreoMarina;
    }

    /**
     * @return int
     */
    public function getDolar()
    {
        return $this->dolar;
    }

    /**
     * @param int $dolar
     */
    public function setDolar($dolar)
    {
        $this->dolar = $dolar;
    }

    /**
     * @return int
     */
    public function getFolioMarina()
    {
        return $this->folioMarina;
    }

    /**
     * @param int $folioMarina
     */
    public function setFolioMarina($folioMarina)
    {
        $this->folioMarina = $folioMarina;
    }

    /**
     * @return int
     */
    public function getDiasHabilesMarinaCotizacion()
    {
        return $this->diasHabilesMarinaCotizacion;
    }

    /**
     * @param int $diasHabilesMarinaCotizacion
     */
    public function setDiasHabilesMarinaCotizacion($diasHabilesMarinaCotizacion)
    {
        $this->diasHabilesMarinaCotizacion = $diasHabilesMarinaCotizacion;
    }

    /**
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param float $iva
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
    }

    /**
     * @return string
     */
    public function getMensajeCorreoMarinaGasolina()
    {
        return $this->mensajeCorreoMarinaGasolina;
    }

    /**
     * @param string $mensajeCorreoMarinaGasolina
     */
    public function setMensajeCorreoMarinaGasolina($mensajeCorreoMarinaGasolina)
    {
        $this->mensajeCorreoMarinaGasolina = $mensajeCorreoMarinaGasolina;
    }


}
